<?php

namespace instagram\smartprint\domain;

class InstagramUser
{
    private $token;
    private $fb_access_token;
    private $num_photos;
    private $last_downloaded_photos;
    private $all_photos_served;
    private $last_photo;
    private $idBooth;
    private $game_code;
    private $total_photos;
    private $type;

    private function __construct($token, $fb_access_token, $total_photos, $num_photos, $last_downloaded_photos, $all_photos_served, $last_photo, $idBooth, $game_code, $type)
    {
        $this->token = $token;
        $this->fb_access_token = $fb_access_token;
        $this->num_photos = $num_photos;
        $this->last_downloaded_photos = $last_downloaded_photos;
        $this->all_photos_served = $all_photos_served;
        $this->last_photo = $last_photo;
        $this->idBooth = $idBooth;
        $this->game_code = $game_code;
        $this->total_photos = $total_photos;
        $this->type = $type;
    }

    public static function of($token, $fb_access_token, $total_photos, $num_photos, $last_downloaded_photos, $all_photos_served, $last_photo, $idBooth, $game_code, $type) {
        return new static($token, $fb_access_token, $total_photos, $num_photos, $last_downloaded_photos, $all_photos_served, $last_photo, $idBooth, $game_code, $type);
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }
     /**
     * @return mixed
     */
     public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getAccessToken()
    {
        return $this->token;
    }

    /**
     * @return mixed
     */
    public function getNumPhotos()
    {
        return $this->num_photos;
    }

    /**
     * @return mixed
     */
    public function getLastDownloadedPhotos()
    {
        return $this->last_downloaded_photos;
    }

    /**
     * @return mixed
     */
    public function getAllPhotosServed()
    {
        return $this->all_photos_served;
    }

    /**
     * @return mixed
     */
    public function getLastPhoto()
    {
        return $this->last_photo;
    }

    /**
     * @return mixed
     */
    public function getIdBooth()
    {
        return $this->idBooth;
    }

    /**
     * @return mixed
     */
    public function getGameCode()
    {
        return $this->game_code;
    }

    /**
     * @return mixed
     */
    public function getTotalPhotos()
    {
        return $this->total_photos;
    }
}