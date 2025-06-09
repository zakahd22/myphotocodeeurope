<?php
require_once '../../../common/conexio.php';
require_once G_PATH . "common/Classes/baseController.php";

/**
 * Class for managing booth data wiping operations
 * Handles deleting booth records and associated files
 */
class BoothWiper {
  private $connection;
  private $controller;
  private $boothId;
  private $logFile = 'booth_wipes';
  
  /**
   * Constructor
   * 
   * @param int $boothId The ID of the booth to wipe
   */
  public function __construct($boothId) {
    global $CLD_CON;
    $this->connection = $CLD_CON;
    $this->boothId = $boothId;
    
    // Initialize controller for model access
    $this->controller = new baseController();
    $this->controller->createModel('App_boothDongle');
    $this->controller->createModel('events');
    $this->controller->createModel('photos');
    
    // Initialize log
    $this->initializeLog();
  }
  
  /**
   * Setup log file with header
   */
  private function initializeLog() {
    utils::log("\n", $this->logFile);
    utils::log("=========================================", $this->logFile);
    utils::log("Starting booth wipe process for booth ID: {$this->boothId}", $this->logFile);
  }
  
  /**
   * Log a message with booth ID context
   * 
   * @param string $message The message to log
   * @param string $level Log level (INFO, WARNING, ERROR, SUCCESS)
   */
  private function log($message, $level = 'INFO') {
    $prefix = "[Booth {$this->boothId}] ";
    
    switch ($level) {
      case 'ERROR':
        $prefix = "ERROR " . $prefix;
        break;
      case 'WARNING':
        $prefix = "WARNING " . $prefix;
        break;
      case 'SUCCESS':
        $prefix = "SUCCESS " . $prefix;
        break;
      default:
        $prefix = "INFO " . $prefix;
    }
    
    utils::log($prefix . $message, $this->logFile);
  }
  
  /**
   * Main method to execute the complete wiping process
   * 
   * @return array Results of the operation
   */
  public function wipeBoothData() {
    $results = [
      'success' => true,
      'deleted_records' => [],
      'errors' => []
      ];
      
    try {
      $this->log("Starting booth data wipe process");
      
      // 1. Delete alerts
      $this->log("Deleting booth alerts");
      $success = $this->deleteTableData('App_boothAlert', 'idBooth');
      $results['deleted_records']['alerts'] = $success ? 'deleted' : 'failed';
      
      // 2. Delete audit records
      $this->log("Deleting booth audit records");
      $success = $this->deleteTableData('App_info', 'idBooth');
      $results['deleted_records']['audit_records'] = $success ? 'deleted' : 'failed';
      
      // 3. Get event IDs before deleting photos
      $this->log("Finding associated events");
      $eventIds = $this->getBoothEventIds();
      $results['deleted_records']['events_found'] = count($eventIds);
      $this->log("Found " . count($eventIds) . " associated events");
      
      // 4. Get event folder data BEFORE deleting events from database
      $this->log("Collecting folder data for events");
      $folderData = [];
      if (!empty($eventIds)) {
        $folderData = $this->getEventFolderData($eventIds);
        $this->log("Collected folder data for " . count($folderData) . " events");
      }
      
      // 5. Delete photos (database records)
      $this->log("Deleting photo records from database");
      $success = $this->deleteTableData('photos', 'pbs_id');
      $results['deleted_records']['photos'] = $success ? 'deleted' : 'failed';

      // 6. Delete events from database
      if (!empty($eventIds)) {
        $this->log("Deleting events from database");
        $success = $this->deleteEvents($eventIds);
        $results['deleted_records']['events'] = $success ? 'deleted' : 'failed';
      }
      
      // 7. Delete event folders using pre-fetched data
      $this->log("Attempting to delete event folders");
      $eventResults = $this->deleteBoothEventFolders($eventIds, $folderData);
      $results['deleted_records']['event_folders'] = count($eventResults['deleted_folders']);
      
      if (!empty($eventResults['deleted_folders'])) {
        $this->log("Successfully deleted " . count($eventResults['deleted_folders']) . " event folders", 'SUCCESS');
      } else {
        $this->log("No event folders were deleted", 'WARNING');
      }
      
      if (!empty($eventResults['errors'])) {
        foreach ($eventResults['errors'] as $error) {
          $this->log($error, 'ERROR');
        }
        $results['errors'] = array_merge($results['errors'], $eventResults['errors']);
      }

    } catch (Exception $e) {
      $results['success'] = false;
      $errorMsg = "Error wiping booth data: " . $e->getMessage();
      $this->log($errorMsg, 'ERROR');
      $results['errors'][] = $errorMsg;
    }
      
    // Log results summary
    if ($results['success']) {
      $this->log("Booth data wipe completed successfully", 'SUCCESS');
    } else {
      $this->log("Booth data wipe completed with errors", 'ERROR');
    }
    
    $this->log("=========================================");
    utils::log(json_encode($results, JSON_PRETTY_PRINT), $this->logFile);
    
    return $results;
  }

  /**
   * Get folder data for events before deleting them
   * 
   * @param array $eventIds Array of event IDs
   * @return array Event data for folder deletion
   */
  private function getEventFolderData($eventIds) {
    $folderData = [];
    
    foreach ($eventIds as $eventId) {
      $eventData = $this->controller->eventsModel->getEvent($eventId);
      
      if ($eventData && isset($eventData[0]['start_date'])) {
        $folderData[$eventId] = [
          'start_date' => $eventData[0]['start_date'],
          'folder_name' => $eventData[0]['start_date'] . $eventId
        ];
      }
    }
    
    return $folderData;
  }
  
  /**
   * Delete data from a specified table for the booth
   * 
   * @param string $tableName Table to delete records from
   * @param string $boothIdColumn Name of the column containing booth ID
   * @return bool Whether the deletion was successful
   */
  private function deleteTableData($tableName, $boothIdColumn) {
    $query = "DELETE FROM $tableName WHERE $boothIdColumn = " . $this->boothId;
    $result = $this->connection->Execute($query);
    
    return $result ? true : false;
  }
  
  /**
   * Get all event IDs associated with this booth
   * 
   * @return array Array of event IDs
   */
  private function getBoothEventIds() {
    $eventIds = [];
    
    // Get events from photos table directly
    $query = "SELECT DISTINCT event_id FROM photos WHERE pbs_id = " . $this->boothId;
    $this->connection->OpenRs($query);
    
    while ($this->connection->FetchArray()) {
      $eventIds[] = $this->connection->GetArrayField("event_id");
    }
    
    // Also get events through dongle associations if needed
    $booths = $this->controller->App_boothDongleModel->boothDongles($this->boothId);
    
    foreach ($booths as $booth) {
      $dongleId = $booth["idDongle"];
      $dateStart = $booth["datetimeS"];
      $dateFinish = empty($booth["datetimeF"]) ? "3000-01-01" : $booth["datetimeF"];
      
      // Get photos connected to this dongle
      $eventsFromPhotos = $this->controller->photosModel->getPhotosScript(
        $dongleId, 
        $dateStart, 
        $dateFinish, 
        true
      );
      
      // Extract event IDs
      if (is_array($eventsFromPhotos)) {
        foreach ($eventsFromPhotos as $eventData) {
          $eventIds[] = $eventData["event_id"];
        }
      }
    }
    
    // Remove duplicates
    return array_unique($eventIds);
  }
  
  /**
   * Delete events from the database
   * 
   * @param array $eventIds Array of event IDs to delete
   * @return bool Whether the deletion was successful
   */
  private function deleteEvents($eventIds) {
    if (empty($eventIds)) {
      return false;
    }
    
    $idList = implode(',', $eventIds);
    $query = "DELETE FROM events WHERE id IN ($idList)";
    $result = $this->connection->Execute($query);
    
    return $result ? true : false;
  }
  
  /**
   * Delete all event folders associated with the booth
   * 
   * @param array $eventIds Array of event IDs to delete
   * @param array $folderData Pre-fetched folder data (optional)
   * @return array Results of the deletion
   */
  private function deleteBoothEventFolders($eventIds = [], $folderData = []) {
    $results = [
      'deleted_folders' => [],
      'errors' => [],
      'debug_info' => [] // For debugging
    ];

    $eventsPath = G_PATH . 'events/';
    
    // Record debug info about G_PATH
    $results['debug_info']['g_path'] = G_PATH;
    $results['debug_info']['path_candidates'] = $eventsPath;
    
    try {
      // If no events found, return early
      if (empty($eventIds)) {
        $this->log("No event IDs provided for folder deletion", 'WARNING');
        $results['debug_info']['no_events'] = true;
        return $results;
      }
        
      $results['debug_info']['event_ids'] = $eventIds;

      // Verify the events directory exists
      if (!is_dir($eventsPath)) {
        $error = "Events directory does not exist: $eventsPath";
        $this->log($error, 'ERROR');
        $results['errors'][] = $error;
        return $results;
      }
      
      $this->log("Using events directory: $eventsPath");
      $results['debug_info']['path_used'] = [
        'path' => $eventsPath,
        'is_readable' => is_readable($eventsPath),
        'is_writable' => is_writable($eventsPath)
      ];
      
      // Process each event folder
      foreach ($eventIds as $eventId) {
        // Use pre-fetched folder data if available
        if (isset($folderData[$eventId])) {
          $folderName = $folderData[$eventId]['folder_name'];
          $folderPath = $eventsPath . $folderName;
          
          $this->log("Checking folder: $folderPath");
          $results['debug_info']['folders_checked'][] = [
              'name' => $folderName,
              'full_path' => $folderPath,
              'exists' => is_dir($folderPath),
              'is_readable' => is_readable($folderPath),
              'is_writable' => is_writable($folderPath)
          ];
          
          // Delete the folder if it exists
          if (is_dir($folderPath)) {
            $this->log("Found folder to delete: $folderName");
            
            // Use system command for more efficient deletion
            exec("rm -rf " . escapeshellarg($folderPath) . " 2>&1", $output, $returnCode);
            
            $results['debug_info']['deletion_attempts'][] = [
              'folder' => $folderName,
              'command_output' => $output,
              'return_code' => $returnCode
            ];
            
            if ($returnCode === 0) {
              $this->log("Successfully deleted folder: $folderName", 'SUCCESS');
              $results['deleted_folders'][] = $folderName;
            } else {
              $this->log("System delete failed for: $folderName, trying PHP fallback", 'WARNING');
              
              // Try PHP deletion if system command fails
              $deleted = $this->deleteDirectoryRecursive($folderPath);
              
              if ($deleted) {
                $this->log("PHP deletion succeeded for: $folderName", 'SUCCESS');
                $results['deleted_folders'][] = $folderName;
              } else {
                $error = "Failed to delete folder: $folderName (Permission denied or other error)";
                $this->log($error, 'ERROR');
                $results['errors'][] = $error;
                
                // Try to determine why deletion failed
                if (file_exists($folderPath)) {
                  $perms = fileperms($folderPath);
                  $results['debug_info']['deletion_failure'][] = [
                    'folder' => $folderName,
                    'permissions' => decoct($perms & 0777),
                    'owner' => fileowner($folderPath),
                    'group' => filegroup($folderPath)
                  ];
                }
              }
            }
          } else {
            $this->log("Folder does not exist: $folderPath");
          }
        } else {
          $error = "No folder data available for event ID: $eventId";
          $this->log($error, 'ERROR');
          $results['errors'][] = $error;
        }
      }
      
    } catch (Exception $e) {
      $error = "Error: " . $e->getMessage();
      $this->log($error, 'ERROR');
      $results['errors'][] = $error;
      $results['debug_info']['exception'] = [
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
      ];
    }
    
    return $results;
  }
  
  /**
   * Delete a directory and all its contents recursively
   * 
   * @param string $dir Path to directory
   * @return bool Whether deletion was successful
   */
  private function deleteDirectoryRecursive($dir) {
    if (!is_dir($dir)) {
      return false;
    }
    
    $files = array_diff(scandir($dir), ['.', '..']);
    
    foreach ($files as $file) {
      $path = $dir . '/' . $file;
      
      if (is_dir($path)) {
        $this->deleteDirectoryRecursive($path);
      } else {
        unlink($path);
      }
    }
    
    return rmdir($dir);
  }
}