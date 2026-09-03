<?php
$lan = "en";
switch ($lan) {
    case "en":
       //MENU ->
        $M_OWNERP = "MY PROFILE";   
        $WELCOME = "WELCOME";
        $M_OWNERS = "OWNERS";
        $M_BOOTHS = "PHOTOBOOTHS";
        $M_EVENTS = "EVENTS";
        $M_PHOTOS = "PHOTOS";
        $M_EMAILS = "EMAILS";
        $M_CUSTOM = "CUSTOMIZE USB STICK";
        $M_INFO = "INFO";
        $M_EXIT = "EXIT";
        $M_COMPONENT = "COMPONENTS";
        $M_INCIDENCIES = "INCIDENTS";
        $M_DONGLE = "DONGLES";
        //Fi menú   
        //PESTANYES DE FILTRES I ACIONS   
        $P_FILTERS = "FILTERS";
        $P_ACCTIONS = "ACTIONS";
        $ACT_EDIT = "EDIT THIS";
        $NORESULTS  = "No results for this query";
        $YES = "YES";
        $NO = "NO";
        $AND = "AND";
        $OPTIONNONE  ="NONE";
        switch ($labels_apartat) {
            case "owner":
                //OWNERS LIST 
                $OL_CODE = "CODE";
                $OL_CPNAME = "COMPANY NAME"; 
               $OL_ADDRESS = "ADDRESS";
                $OL_CONTACT = "CONTACT";
                $OL_USERNAME = "USERNAME";
                $OL_PASSWORD = "PASSWORD";
                //FI OWNER LIST
                //OWNER FITXA
                $OF_TITLE = "OWNER PROFILE";
                $OF_CODE = "CODE";
                $OF_USERNAME = "USERNAME";
                $OF_PASSWORD = "PASSWORD";
                $OF_EVENTS = "EVENTS";
                $OF_PHOTOBOOTHS = "PHOTOBOOTHS";
                ;
                $OF_CONTACTS = "CONTACTS";
                $OF_ADDRESSES = "ADDRESSES";
                //OWNER FITXA USER OWNER
                $OO_NAME = "OWNER NAME";
                $OO_SETPASS = "CREATE PASSWORD";
                $OO_NEWPASS = "NEW PASSWORD :";
                $OO_REPEATPASS = "REPEAT :";
                $OO_BUTTONUPDATE = "UPDATE";
                $OO_ADDADDRESS = "ADD ADDRESS";
                $OO_ADDRESS = "ADDRESS :";
                $OO_CITY = "CITY :";
                $OO_STATE = "STATE :";
                $OO_ADDRESS_CODE = "ZIP CODE :";
                $OO_COUNTRY = "COUNTRY : ";
                $OO_ADDCONTACT = "ADD CONTACT";
                $OO_CONTACTNAME = "NAME :";
                $OO_CONTACTSURNAMES = "LAST NAME :";
                $OO_CONTACTRANK = "JOB TITLE :";
                $OO_CONTACTPHONE = "PHONE :";
                $OO_CONTACTEMAIL = "E-MAIL :";
                $OO_CONTACTUS = "CONTACT US";
                $OO_MYADDRESSES = "MY ADDRESSES";
                $OO_CONTACT_CITY = "CONTACT CITY"; // AFTER REV
                //
                //EDIT OWNER
                $OE_NME =  "NAME & LAST NAME"; //AFTER REV
                $OE_JOBTITLE = "JOB TITLE"; //AFTER REV
                $OE_DEFAULT = "DEFAULT/SET DEFAULT"; //AFTER REV
                $OE_PHONE = "PHONE"; //AFTER REV
                $OE_EMAIL = "EMAIL"; // AFTER REV
                $OE_CITY = "CITY";//AFTER REV
                $OE_ADDRESS = "ADDRESS";//AFTER REV
                $OE_STATE = "STATE"; //AFTER REV
                $OE_COUNTRY = "COUNTRY";//AFTER REV
                $OE_ZIPCODE = "ZIP CODE"; //AFTER REV
          
                
                $OE_INFORMATION = "OWNER INFORMATION";
                $OE_ADDRESSES = "ADDRESSES";
                $OE_CONTACTS = "CONTACTS";
                //NEW OWNER
                /*
                $NWO_NAME = "NAME";
                $NWO_CODE = "CODE";
                $NW0_EMAIL = "EMAIL";
                */
              
                
                //CONTINGUT DE PASTANYES DE FITXA OWNER
                    $PEST_OW_NUMOFBOOTHS  = "NUMBER OF PHOTOBOOTHS : ";
                     $PEST_OW_NUMOFEVENTS = "NUMBER OF EVENTS :";
                     $_EXTRA_EVENT_NAME = "EVENT NAME";
                     $_EXTRA_EVENT_DATE  = "EVENT DATE";
                     $_EXTRA_EVENT_PHOTOS = "PHOTOS";
                     $_EXTRA_BOOTH_NAME = "BOOTH NAME";
                     $_EXTRA_BOOTH_LOCATION = "LOCATION";
                     $_EXTRA_BOOTH_TYPE = "TYPE";
                     $_EXTRA_CONTACT_CARREC = "RANK";
                     $_EXTRA_CONTACT_CITY = "CITY";
                     $_EXTRA_CONTACT_EMAIL= "EMAIL";
                     $_EXTRA_CONTACT_NAME  = "NAME";
                     $_EXTRA_CONTACT_PHONE = "PHONE";
                
                //FILTERS OWNER
                $FO_CODE = "CODE";
                $FO_CPNAME_CONTACT = "COMPANY NAME / CONTACT";
                $FO_LOCATION = "ZIPCODE/CITY/STATE/COUNTRY/";

                //POPUPLIST OWNER

                $OPL_CODE = "CODE";
                $OPL_NAME = "COMPANY NAME";

                //POPUP FILTERS OWNER

                $OPF_CODE = "CODE";
                $OPF_CONTACT_NAME = "COMPANY NAME/CONTACT";
                $OPF_CTY = "CITY / COUNTRY";

                //ACTIONS OWNER
                $ACT_EDIT = "EDIT THIS";
                $ACT_NEW_ADDRESSES = "VIEW NEW ADDRESSES";
                
                //INFOS 
                $ADD_CONTACT_TEXTINFO = "With this form, can be added different contact persons of the company. Like in the last form,when all the information is typed, please cilck ADD CONTACT and the new contact will be created.";
                $ADD_ADDRESS_TEXTINFO = "In this form can be added different addresses of the company. When all the information is typed, please cilck ADD ADDRESS and the new address will be created.";
                $MYPROFILE_TEXTINFO = "<div class='textInfo'>In this form you can change : <ol><li>NAME: name of the owner&#039;s company (Not the owner&#039;s name).</li><li>PASSWORD: password to log in.</li><li>ALERTS EMAILS: email, which will recieve an alert when, for example, the PhotoBooth running out of film, has the cash box full or run out of Internet. To recieve this email the alerts must be activated.</li></ol>To edit this fields, please type, on the text box, the new information and cilck the <b>UPDATE</b> button.</div>";
                break;
            case "photobooths":
                //PHOTOBOOTHS LIST
                $BL_SERIALNUMBER = "S/N";
                $BL_NAME = "NAME";
                $BL_TYPE = "TYPE";
                $BL_OWNER = "OWNER";
                $BL_SOFTWAREVERSION = "VERSION";
                $BL_LOCATION = "LOCATION";
                $BL_STATUS = "STATUS";
                $BL_ASSIGNOWNER  = "ASSIGN OWNER";
                //FITXA PHOTOBOOTH
                $BF_PHOTOBOOTH = "PHOTOBOOTH PROFILE";
                $BF_SERIALNUMBER = "SERIAL NUMBER";
                $BF_STATUS = "STATUS";
                $BF_VERSION = "LAST SOFTWARE VERSION";
                $BF_OWNER = "OWNER";
                $BF_LOCATION = "LOCATION";
                $BF_COMPONENTS = "COMPONENTS";
                $BF_EVENTS = "EVENTS";
                $BF_ALERTS = "ALERTS";
                $BF_DONGLE = "DONGLE PAIRINGS";
                $BF_PAYPAL = "PAYPAL";
                $BF_STATISTICS = "PHOTOBOOTH STATISTICS";
                
                //EDIT PHOTOBOOTH
                $EDIT_BOOTH_INFO = "BOOTH INFORMATION";
                $EDIT_BOOTH_NAME = "PHOTOBOOTH NAME";
                $EDIT_BOOTH_LOCATION = "LOCATION PHOTOBOOTH";
                $EDIT_BOOTH_COORDS = "COORDENATES LATITUDE AND LONGITUDE";
                $EDIT_BOOTH_BUTTON_UPDATE = "UPDATE";
                $EDIT_BOOTH_EXTRA = "EXTRA ACTIONS";
                $EDIT_BOOTH_FRASE1 = "This PhotoBooth is in production, if you want change it to stock, please cilck the button:";
                $EDIT_BOOTH_BUTTON_TOSTOCK = "SET TO STOCK";
                $EDIT_BOOTH_FRASE2 = "This PhotoBooth is in stock, if you want change it to sold, please cilck the button:";
                $EDIT_BOOTH_BUTTON_TOSOLD = "SET TO SOLD";
                $EDIT_BOOTH_PAIRING = "CREATE NEW PAIRING";
                $EDIT_BOOTH_FRASE3 = "Choose the new paring";
                $EDIT_BOOTH_BUTTON_PAIRING = "CREATE NEW PAIRING";
                $EDIT_BOOTH_FRASE4 = "You can assign a owner or set the assigned owner.";
                $EDIT_BOOTH_BUTTON_SETOWNER = "SET ASSIGNED OWNER";
                $EDIT_BOOTH_COMP = "EDIT THE COMPONENTS OF PHOTOBOOTH";
                //FILTRES PHOTOBOOTH      
                $FB_SERIALNUMBER = "S/N";
                $FB_OWNER = "OWNER(double click)";
                $FB_TYPE = "TYPE";
                $FB_STATUS = "STATUS";
                $FB_DONGLE = "DONGLE STRING";
                $FB_SELECT_OPTION0= "Pending";
                $FB_SELECT_OPTION1= "Production";
                $FB_SELECT_OPTION2="Stock";
                $FB_SELECT_OPTION3="Sold";
                $FB_UNOWNED =" UNOWNED";
                //ACTIONS PHOTOBOOTH;
                $AB_NEWSERIE = "NEW SERIE PRODUCTS";
                $AB_NEWPRODUCT = "NEW PRODUCT"; 
                //NEW PHOTOBOOTH
                $NEW_BOOTH_FRASE1 = "Use this form to create a new Product";
                $NEW_BOOTH_NAME = "NAME";
                $NEW_BOOTH_NUMMODEL = "MODEL NUMBER";
                $NEW_BOOTH_NUMSCREENS = "SCREENS NUMBER";
                $NEW_BOOTH_LOGO = "LOGO";
                $NEW_BOOTH_WIDTH = "WIDTH";
                $NEW_BOOTH_HEIGHT = "HEIGHT";
                $NEW_BOOTH_FRAMES = "FRAMES";
                $NEW_BOOTH_WELCOME= "WELCOME PAGE";
                $NEW_BOOTH_BANNER = "BANNER";
                $NEW_BOOTH_CUSTOMS = "CUSTOMS";
                $NEW_BOOTH_FRASE2= "Select the components and quantity: ";
                $NEW_BOOTH_BUTTON_CREATE= "CREATE";
                //NEW PRODUCTION
                $NEWP_BOOTH_FRASE1 = "New production of PhotoBooths";
                $NEWP_BOOTH_FRASE2 = "SELECT A NEW SERIAL OF PRODUCTS:";
                $NEWP_BOOTH_FRASE3 = "QUANTITY";
                $NEWP_BOOTH_BUTTON_CREATE = "CREATE";
                
                //PHOTOBOOTHS EXTRA PESTANYES 
                        //ALERTS
                        $PEST_ALERTS_OPTION1 = "NEW ALERTS";
                        $PEST_ALERTS_OPTION2 = "OLD ALERTS";
                        $PEST_ALERTS_OPTION3 = "OFFLINE";
                        $PEST_ALERTS_FRASE1 = "This PhotoBooth doesn&#039;t have any new alerts";
                        $EXTR_ALERTS1 =  "You have not a email defined for receive the notifications. Go to your Information and change the alert email.";//AFTER REV
                        $EXTR_ALERTS2 = "ASSIGNED email :"; //AFTER REV
                        //DONGLE
                        //-------
                        //EVENTS
                        $PEST_EVENTS_FRASE1 = "This PhotoBooth go to ";
                        $PEST_EVENTS_FRASE2 = " events.";
                        //LOCATION
                        $PEST_LOCATION_TEXTINFO = "Please Type the NAME of the BUSINESS where the Photobooth is Located, as well as the LATITUDE and LONGITUDE.  NAME will appear every time Patrons post on Social Networks or send to a Friend. Longitude and Latitude is Necessary to Track your Photobooth thru the APPs, as well as Patrons to locate your PhotoBooth.";
                        $PEST_LOCATION_LOC = "LOCATION :";
                        $PEST_LOCATION_LATITUDE = "LATITUDE :";
                        $PEST_LOCATION_LONGITUDE = "LONGITUDE :";
                        $PEST_LOCATION_BUTTON_UPDATE = "UPDATE";
                        $PEST_LOCATION_BUTTON_LOOK = "LOOK";
                        //COMPONENTS
                        $PEST_COMP_FRASE1 = "Quality control has been passed by :";
                        $PEST_COMP_TOSTOCK = "TO STOCK";
                        $PEST_COMP_DONGLE = "Dongle";
                        $_EXTRA_COMPON_SN = "SERIAL NUMBER";
                        $_EXTRA_COMP_MARK  = "MARK";
                        $_EXTRA_COMP_MODEL = "MODEL";
                        $_EXTRA_COMP_TYPE = "TYPE";
                        $_EXTRA_PAIR_DONGLE  = "DONGLE STRING";
                        $_EXTRA_PAIR_START = "START DATE";
                        $_EXTRA_PAIR_FINISH ="END DATE";

                        //PHOTOS
                        $PEST_PHOTOS_FRASE1 = "THIS PHOTOBOOTH HAVE ";
                        $PEST_PHOTOS_FRASE2 = "PHOTOS (code , date , inaPpropiated).";
                        //PAYPAL
                        $EXTR_PAYPAL1 = "The Merchant account ID of your paypal account is"; //AFTER REV
                        $EXTR_PAYPAL2 = ". You can update it here:";//AFTER REV
                        $EXTR_PAYPAL3 = "The Merchant account ID was developed as a substitute for your email address to prevent spam bots from harvesting your email address on web site pages that contain your item button code.  The Merchant account ID is sometimes referred to as your PayPal Account ID Number. ";//AFTER REV
                        $EXTR_PAYPAL4 = "Method PayPal Button";//AFTER REV
                        $EXTR_PAYPAL5 =  "If you want to disable, please leave the Merchant account ID field empty and click on update"; //AFTER REV
                        $EXTR_PAYPAL6 = "To activate paypal, please type your paypal Merchant account ID here and click on update."; //AFTER REV
                        $EXTR_PAYPAL7 =  "MERCHANT ACCOUNT ID :";//AFTER REV 
                        
                break;
            case "events":
                //ACTIONS EVENTS
                   $ACT_EVENTS_NE = "NEW EVENT";
                //EVENTS LIST    
                $EL_NAME = "EVENT NAME";
                $EL_DATE = "START DATE";
                $EL_OWNER = "OWNER";
                $EL_PHOTOS = "PHOTOS";
                //LIST EVENT MANEGER 
                $LIST_CUSTOM = "Custom : ";
                //EVENT FITXA
                $EF_TITLE = "EVENT";
                $EF_OWNER = "OWNER";
                $EF_DATE = "DATE";
                $EF_PRIVATE = "This event is private";
                $EF_NOPRIVATE = "This event is not private";
                $EF_AUTOCREATED = "This event is autocreated";
                $EF_PHOTOS = "PHOTOS";
                $EF_PHOTOBOOTHS = "PHOTOBOOTHS";
                $EF_EVENTMANAGER = "EVENT MANAGER";
                $EF_BACKGROUND = "BACKGROUND";
                $EF_BANNER = "BANNER";
                $EF_QUESTIONS = "QUESTIONS";
                $EF_EMAILS = "EMAILS";
                $EF_BACKGROUND_SELECT = "BACKGROUND";
                $EF_BANNER_SELECT = "BANNER";
                
                //FILTRES EVENTS

                $FE_NAME = "EVENT NAME";
                $FE_OWNER = "OWNER (double click)";
                $FE_DATE1 = "BETWEEN";
                $FE_DATE2 = "AND";
                //NEW EVENT
                $NE_STEP1 = "TITLE1";
                $NE_STEP2 = "TITLE2";
                $NE_STEP3 = "TITLE3";
                $NE_NAME = "EVENT NAME";
                $NE_DATE = "EVENT DATE";
                $NE_QUINS_BOOTHS_QUESTION = "SELECT THE PHOTOBOOTH OF THE EVENT";
                $NE_PRIVATE = "PRIVATE?";
                $NE_AVIABLE = "AVAILABLE ONLINE?";
                $NE_SINGLEDAY = "SINGLE DAY EVENT?";
                $NEW_EVENT_INFORMATION = "EVENT INFORMATION";
                $NEW_EVENT_BUTTON_CREATE = "CREATE";
                //EDIT EVENTS
                $EDIT_EVENT_INFORMATION = "EVENT INFORMATION";
                $EDIT_EVENT_BUTTON = "UPDATE";
                $EDIT_EVENT_DATE = "(mm/dd/yyyy)";
          
                
                
                //EXTRA EVENTS
                    //BACKGROUNDS
                        $EVENT_BACKGROUND_SELECT = "<b>Default background option</b>"; //AFTER REV
                        $EVENT_BACKGROUND_UPLOAD = "Or check the checkbox to save a new background:";
                       
                        $EVENT_CUSTOM = "";
                        $EVENT_SELECTED_BACKGROUND = "This is your selected background: ";//AFTER REV
                        $EVENT_NOBACKGROUND = "This event has no background";//AFTER REV
                        $EVENT_BACKGROUNDINFO = "This is the background that users will see when they access their event photos via the MyPhotoCode cloud.";
                        $EVENT_BACKGROUNDINFO2 = "Save an JPG image or select a default background to set the background of this event.";
                        $BACKGROUND_TITLE1 = "BACKGROUND PREVIEW";
                        $BACKGROUND_TITLE = "SAVE/SELECT A NEW BACKGROUND";
                    //EMAILS
                        $EMAILS_TEXTINFO = "This list records the emails that users enter when choosing to share their photos via email, after accessing their photos via the MyPhotoCode cloud. All emails can be downloaded as an excel file, simply click Download Emails.";
                        $EVENT_EMAILS_FRASE1 = "This event has collected ";
                        $EVENT_EMAILS_FRASE2 = " emails";
                        $EVENT_BUTTON_DOWNLOAD = "DOWNLOAD EMAIL LIST";
                        $_EXTRA_EMAIL = "EMAIL";
                        $_EXTRA_EMAIL_DATE = "DATE";
                        $_EXTRA_EMAIL_PHOTO = "PHOTOCODE";
                   //EVENTMANEGER
                        $EVENT_MAN_INFOTEXT = "You can authorize another person to be an Event Manager. Event Managers can log into myphotocode.com and customize the designated event. Type the name and email of the person you want to designate as an Event Manager. That person will be sent an email allowing them to register and start customising the event.";
                        $EVENT_MAN_INVNAME = "INVITED NAME";
                        $EVENT_MAN_INVEMAIL = "INVITED EMAIL";
                        $EVENT_MAN_BUTTON_INVITE = "INVITE!";
                        $EVENT_MAN_INVEMAIL2 = "INVITED EMAIL: ";
                        $EVENT_MAN_INVNAME2 = "INVITED NAME: ";
                        $EVENT_MAN_REGNAME = "REGISTRED NAME: ";
                        $EVENT_MAN_REGEMAIL = "REGISTERED EMAIL: ";
                        $EVENT_MAN_SECODE = " SECURITY CODE: ";
                         $EVENT_MAN_RESENDEMAIL = "RESEND EMAIL";
                   //PHOTOBOOTHS
                        $EVENT_BOOTH_BUTTON_UPDATE = "COMFIRM";
                        $EVENT_BOOTH_FRASE1 = "The owner has not assigned any PhotoBooth";
                        $EVENT_BOOTHS_INEVENT = "PHOTOBOOTH IN THIS EVENT :";
                  //PHOTOS
                        $EVENT_PHOTOS_SEL1 = "THUMBS";
                        $EVENT_PHOTOS_SEL2 = "LIST";
                        $EVENT_PHOTOS_IMPORT_BUTTON = "IMPORT PHOTOS FROM OTHER EVENT";
                        $EVENT_PHOTOS_FRASE1 = "THIS EVENT HAVE ";
                        $EVENT_PHOTOS_FRASE2 = "PHOTOS (code, date, inaPpropiate)";
                        $EVENT_PHOTOS_FACEBOOK_BUTTON = "SHARE ALL TO FACEBOOK";
                        $_EXTRA_PHOTO_CODE = "CODE";
                        $_EXTRA_PHOTO_DATE ="DATE";
                        $_EXTRA_PHOTO_FLAG = "INAPPROPIATE";
                        $APPROPITED = "APROPIATE";
                        $INAPROPIATED = "INAPPROPIATE";
                  //QUESTIONS
                        $EVENT_QUESTION_ASK_EMAIL_ACTIVE = "<b>The e-mail is active.</b><br> If you want to disable it, please click the checkbox";
                        $EVENT_QUESTION_ASK_EMAIL_DESACTIVE = "<b>The e-mail is disable.</b><br> If you want to activate it, please cilck the checkbox";
                        $EVENT_QUESTION_EMAIL_NUM = "e-mails are saved";
                        $QUESTION = "Question ";
                        $IS_ACTIVE = " is active";
                        $IS_DISABLE = " is disable";
                        $CONDICIO_DISABLE = "<br> If you want to disable it, please cilck the checkbox";
                        $CONDICIO_ACTIVE = "<br> If you want to activate it, please cilck the checkbox";
                        $CLICKED = "clicked";
                        $REPLY_1 = "Opinion 1 :";
                        $REPLY_2 = "Opinion 2 :";
                        $EVENT_QUESTIONS_EXPLICACIO = "Switch ON the questionnaire module to ask guest (photobooth users) for their email address and opinion. When switched ON, users must first enter their email address and respond to the questions before they can digitally access their photos from the MyPhotoCode cloud.";  
                        $EVENT_QUESTIONS_EXPLICACIO2 = "Enable to ask users opinion.";
                        $EVENT_QUESTIONS_EXPLICACIO1 = "Enable to capture the email address from all users that want to access their photos.";
                        $QUESTIONS_TITLE = "QUESTIONNAIRE"; 
                  //BANNER
                        $BANNER_TITTLE1 = "BANNER PREVIEW";
                        $BANNER_TITTLE2 = "SAVE A NEW BANNER/URL";
                        $EF_BANNER_SELECTED = "THE BANNER SELECTED IS : ";
                        $EF_URL_BANNER = "BANNER LINK URL :";
                        $EF_URL_BANNER1 = "Actual link :";
                        $EF_EVENT_FRASE1= "type an url or leave empty (example : <span title='Very Important!'><b><u>https://</u></b></span>https://www.myphotocode.com)";
                        $EF_EVENT_FRASE2 = "Choose a file to change the banner (Max-width:500px). ";
                        $EF_BANNER_ACTIVE = "The banner is <b>ON</b>, to switch <b>OFF</b> please click the checkbox: ";//AFTER REV
                        $EF_BANNER_DISABLE = "The banner is <b>OFF</b>, to switch <b>ON</b> please click the checkbox: ";//AFTER REV
                        $INFO1 = "Type a url link ('https://www.example.com') and save an image to set the banner of this event.";
                        $INFO_SELECTEDBANNER = "This is the banner and url link that  users see when they acces the Cloud to get their Photo/Video from this event.";
             //POPUPFILTERS
                 $EVENT_POPUP_NAME = "EVENT NAME";
                 $EVENT_POPUP_BETWEEN = "BETWEEN";
                 $EVENT_POPUP_AND  ="AND";
                 $EVENT_POPUP_BUTTON = "SEARCH";
                break;
            case "photos":
                //PHOTOS LIST
                $PL_CODE = "PHOTOCODE";
                $PL_EVENT = "EVENT";
                $PL_EVENTDATE = "EVENT DATE";
                $PL_BOOTH = "DONGLE";
                $PL_INAPROPIATE = "INAPPROPRIATE";
                
                
                //FILTRES PHOTOS
                $FP_CODE = "CODE";
                $FP_OWNER = "OWNER (double click)";
                $FP_EVENT = "EVENT (double click)";
                

                
                break;
            case "emails":
                //EMAIL LIST
                $EML_CODE = "PHOTOCODE";
                $EML_EMAIL = "EMAIL";
                $EML_DATE = "DATE";

                //FILTRES EMAILS
                $FEM_OWNER = "OWNER (double click)";
                $FEM_EVENT = "EVENT (double click)";
                
                //ACCTIONS EMAILS
                $ACT_EMAILS_DOWNLOAD = "DOWNLOAD EMAILS";
                
                break;
            case "components":
                
                //COMPONENTS LIST
                $CL_MARCA = "MARCA";
                $CL_MODEL = "MODEL";
                $CL_CONTROLABLE = "CONTROLLABLE";
                $CL_TYPE = "TYPE";
                $CL_SERIALNUMBER = "SERIAL NUMBER";
                $CL_CONTROLABLE = "CONTROLLABLE";
                $CL_OWNER ="OWNER";
                //COMPONENTS FITXA
                $CF_COMPONENT = "COMPONENT PROFILE";
                $CF_SN = "SERIAL NUMBER";
                $CF_TYPE = "COMPONENT TYPE";
                //$CF_CONTROLABLE = "IS CONTROLLABLE :";
                $CF_BOOTH = "PHOTOBOOTH";
                $CF_OWNER = "OWNER";

                //COMPONENTS FILTERS
                $FC_SNUMBER = "SERIAL NUMBER";
                $FC_OWNER = "OWNER (double click)";
                $FC_TYPE = "TYPE";
                
                //ACTIONS 
                $ACT_BUTTON_NEWCOMPONENT = "NEW COMPONENT";
                
                //EDIT 
                $EDIT_COMP_FRASE1 = "Select the distributor and cilck the button to assign the component";
                $EDIT_COMP_BUTTON_DIS = 'ASSIGN TO A DISTRIBUTOR';
                $EDIT_COMP_FRASE2_1 = "This component was assigned to the distributor ";
                $EDIT_COMP_FRASE2_2 = "on ";
                $EDIT_COMP_FRASE3 = "Select the owner and cilck the button to assign the component";
                $EDIT_COMP_BUTTON_OWNER = 'ASSIGN TO AN OWNER';
                $EDIT_COMP_FRASE4_1 = "This component was assigned to the owner";
                
                //NEW COMPONENT
                $NEW_COMP_FRASE1 = "In this form you can create a new type of component:";
                $NEW_COMP_MARK = "MARK :";
                $NEW_COMP_MODEL = "MODEL :";
                $NEW_COMP_CONTROLABLE = "CONTROLLABLE";
                $NEW_COMP_NOCONTROLABLE= "NO CONTROLLABLE";
                $NEW_COMP_NEWTYPE = "NEW TYPE :";
                $NEW_COMP_EXISTING = "OR EXISTING TYPE";
                $NEW_COMP_BUTTON_NEW = "CREATE COMPONENT";
                $NEW_COMP_FRASE2= "In this form you can insert serial numbers of components :";
                $NEW_COMP_SN ="SERIAL NUMBER : ";
                $NEW_COMP_BUTTON_NEWSN = "ADD COMPONENT";
                break;
            case "customs":
                //CUSTOMS ACTIONS
                $ACT_CUSTOMS_NEW = "CUSTOMIZE NEW USB";
                //FILTERS
                $FCUS_NAME  = "NAME";
                $CUSB_OWNER = "OWNER (double click)";
                $CUSM_EVENT ="EVENT (double click)";
                $CUSF_TYPE = "PHOTOBOOTH TYPE";
                
                //CUSTOMS FITXA 
                $UPLOAD_A = "Save a ";
                $FIT_CUST_DATE = "CREATION DATE: ";
                $FIT_CUST_EVENT = "EVENT: ";
                $FIT_CUST_BOOTH = "PHOTOBOOTH TYPE ";
                $FIT_CUST_LOGO = "LOGO";
                $FIT_CUST_FRASE1 = "Save your image (This will be resized to ";
                $FIT_CUST_TEXT = "TEXT";
                $FIT_CUST_FRASE2 = "This text will be printed on the side of each print.";
                $FIT_CUST_BACKGROUND = "BACKGROUND MUSIC";
                $FIT_CUST_FRASE3 = "Save any mp3 music file to play in the background of photo sessions. (Max. 5mb).";
                $FIT_CUST_FRAMES = "FRAMES";
                $FIT_CUST_FRASE4 = "Choose any premade frames or save your own. (New frames will be resized to ";
                $FIT_CUST_FRASE5 = "Default Frames:";
                $FIT_CUST_BUTTON_UPDEFAULT = "SAVE DC FRAME";
                $FIT_CUST_FRASE56 = "Custom Frame:";
                $FIT_CUST_BUTTON_UPCUSTOM = "SAVE CUSTOM FRAME";
                $FIT_CUST_FRASE7 = "Frames complete, if you want to save a new one, you should delete one";
                $FIT_CUST_WELCOME = "WELCOME SCREEN";
                
                $FIT_CUST_FRASE8 = " image to be displayed as your Welcome Screen at the start of each photo session.";
                $FIT_CUST_BUTTON_UPWELCOME = "SAVE WELCOMES";
                $FIT_CUST_FRASE9 = "Welcomes complete, if you want to save a new one, you should delete one";
                $FIT_CUST_FRASE10 = "Welcome screens";
                $FIT_CUST_BYE = "BYE SCREEN";
                $FIT_CUST_FRASE11 = "image to be displayed as your Goodbye Screen at the end of each photo session.";
                $RANDOM = "Random";
                $SINGLE = "Single";
                $FIT_CUST_BUTTON_UPBYE = "SAVE BYES";
                $FIT_CUST_FRASE12 = "Byes complete, if you want to save a new one, you should delete one.";
                $FIT_CUST_BANNER = "HEADER (Wedding version)"; 
                $FIT_CUST_FRASE13 = "image which will be placed on the top screen.";
                $FIT_CUST_CUST = "CUSTOM IMAGES ON DEMO SCREEN";
                $FIT_CUST_FRASE14 = "images which will be randomly displayed on the demo.";
                $FIT_CUST_BUTTON_UPCUSTIMAGE = "SAVE CUSTOM IMAGE";
                $FIT_CUST_FRASE15 = "Custom images are complete, if you want to save a new one, you should delete one.";
                $FIT_CUST_DOWNLOAD = "DOWNLOAD CUSTOM USB";
                
                //CUSTOMS LIST
                $CSL_TITLE  = "NAME";
                $CSL_DATECREATION  ="DATE";
                $CSL_OWNER_NAME  ="OWNER";
                $CSL_EVENT  ="EVENT";
                $CSL_BOOTHTYPE  ="TYPE PHOTOBOOTH";
                
                //NEW CUSTOM
                $NEW_CUST_NAME = "NAME";
                $NEW_CUST_FRASE1 = "Used to identify the USB Set Up in the list.";
                $NEW_CUST_BOOTH = "PHOTOBOOTH";
                $NEW_CUST_FRASE2 = "Choose the PhotoBooth model where the USB will be plugged in.";
                $NEW_CUST_EVENT= "ONLINE EVENT";
                $NEW_CUST_FRASE3 = "Is the PhotoBooth going to be linked to any Online Event?";
                $NEW_CUST_FRASE4 = "If so, choose it from the list of already created events.";
                $NEW_CUST_BUTTON_CREATE = "CREATE CUSTOM USB";
                break;
            case "dongle":
                //EDIT 
                $EDIT_DON_INF = "INFORMATION";
                $EDIT_DON_DON  ="DONGLE :";
                $EDIT_DON_REF = "REFERENCE :";
                $EDIT_DON_OWNER = "OWNER";
                $EDIT_BUTTON_SETOWNER = "SET OWNER";
                $EDIT_DON_FRASE1 = "Set the dongle owner. Set the value in dropdown and click $EDIT_BUTTON_SETOWNER";
                $EDIT_DON_PAIR = "CREATE NEW PAIRING";
                $EDIT_DON_FRASE2 =" Choose the new pairing";
                $EDIT_DON_BUTTON_NEWPAIR = "CREATE NEW PAIRING";
                //FILTERS
                $FC_SNUMBER= "DONGLE STRING";
                $FC_OWNER  ="OWNER (double click)";
                //FITXA
                $BF_DONGLE = "DONGLE PROFILE";
                $DF_OWNER = "OWNER :";
                $DF_REFERNECE = "REFEERENCE :";
                $DF_LAST_PHOTOBOOTH = "LAST PHOTOBOOTH :";
                $DF_PEST_BOOTH = "PHOTOBOOTHS&#39;S PAIRINGS";
                //LIST 
                $DL_DONGLE = "DONGLE";
                $DL_REFERENCE = "REFERENCE";
                $DL_STRING = "STRING";
                $DL_OWNER = "OWNER";
                $DL_LASTBOOTH = "LAST PHOTOBOOTH";
                //EXTRA
                    //PHOTOBOTHS
                    $DEX_FRASE1 = "PHOTOBOOTHS&#39;S PAIRINGS";
                    $_EXTRA_DONGLE_START= "START DATE";
                    $_EXTRA_DONGLE_END = "END DATE";
                    $_EXTRA_DONGLE_BOOTH_NAME = "PHOTOBOOTH NAME";
                    $_EXTRA_DONGLE_BOOTH_SN = "PHOTOBOOTH SN";
                break;
            case "errors":
                //TOT EL QUE RETORNAN LES FUNCIONS
                $F1 = "The file ";  
                $F2 = " has been saved";
                $F3 = "There was an error saving the file, please try again!";
                $F4 = "- Address is empty";
                $F5="- City is empty";
                $F6 = "- State is empty";
                $F7 = "- Zip code is empty";
                $F8 = "- Country is empty";
                $F9 = "Error in update , please try again";
                $F10 = " Name is empty - " ;
                $F11 = " last name is empty - ";
                $F12 = " Job title is empty - ";
                $F13 = " Phone is empty - ";
                $F14 = " Email is empty - ";
                $F15 = " City is empty - ";
                $F16 =  " The Name is empty -";
                $F17 = "The Screen is zero or empty -";
                $F18 = " The serial number model is empty -";
                $F19 = "INVITED EMAIL IS EMPTY - ";
                $F20 = "INVITED EMAIL IS NOT VALID";
                $MAIL_SUBJECT =  "Register and edit your event";
                $F21 = "Could not open archive";
                $F22 = "ERROR: Could not add file: ";
                $F23 = "Event Name is empty -";
                $F24 ="Event Date is empty -";
                $F25 = "The serial number is empty";
                $F26 = "The component has been introduced correctly";
                $F27_1 = "The serial number ";
                $F27_2 = "already exists";
                $F28 =" USERNAME is empty";
                $F29 = "The username must be at least 6 characters";
                $F30 = "PASSWORD is empty";
                $F31 ="The password must be at least 8 characters";
                $F32 = "Company Name is empty -";
                $F33 = " Contact Name is empty -";
                $F34 = " Contact last name is empty -";
                $F35 = " Email is not correct -";
                $F36 = " Contact Phone is empty -";
                $F37 = " Contact Post is empty -";
                $F38 = " Contact City is empty -";
                $F39 = " Company Street address is empty -";
                $F40 = " Company City is empty -";
                $F41 = " Company Zip Code is empty -";
                $F42 = " Company Country is empty -";
                $F43 = " Company State is empty -";
                $F44 = " Photobooth Address is empty -";
                $F45=" Photobooth City is empty -";
                $F46 = " Photobooth State is empty -";
                $F47 = " Photobooth Zip Code is empty -";
                $F48= " Photobooth Country is empty -";
                $F50 = "You have assigned the serial numbers of the components";
                $F51 = "You have assigned the Dongle";
                $F52 = "You have assigned the Quality Control person";
                $F53 = "Event Name is empty -";
                $F54 = "Event Date is empty -";
                $F55 =  "Owner Name is empty -";
                $F56 = "Passwords are not the same";
                break;
            case "function_register":
                $REG_TITLE = "MYPHOTOCODE";
                $REG_ERROR1 = " Name is empty ";
                $REG_ERROR2 = " Email isn't valid ";
                $REG_ERROR3 = " Password is empty ";
                $REG_ERROR4 = " Passwords are not the same ";
                $REG_ERROR5 = "The Security code is not correct";
                $REG_NAME = "Name:";
                $REG_SURNAMES = "Last name:";
                $REG_EMAIL = "Email(username):";
                $REG_PASSWORD = "Password:";
                $REG_REPEAT = "Repeat:";
                $REG_SECURITY_CODE = "Security Code:";
                $REG_BUTTON_REGISTER = "REGISTER";
                $REG_ERROR6= "For this event already has a manager";
                break;
            case "popup":
                //owners
                    $OPL_CODE = "CODE";
                    $OPL_NAME = "COMPANY NAME";
                    $OPL_NEWOWNER = "Double Click here to create a new owner";
                    //events
                    $EPL_NAME = "NAME";
                    $EPL_STARTDATE = "START DATE";
                    $EPL_OWNER = "OWNER";
                    //import photos
                    $IMP_PHOTOS_FRASE1 = "Select the event you want to take the photos";
                    $IMP_BUTTON_IMPORT="IMPORT";
                    //NEW OWNER
                    $NEW_OWNER_CMPNAME = "COMPANY NAME:";
                    $NEW_OWNER_USERNAME = "USERNAME: ";
                    $NEW_OWNER_PASS = "PASSWORD:";
                    $NEW_OWNER_CONTACT_PRINCIPAL = "PRINCIPAL CONTACT";
                    $NEW_OWNER_NAME = "NAME";
                    $NEW_OWNER_SURNAME = "LAST NAME";
                    $NEW_OWNER_CONTACT_EMAIL = "EMAIL";
                    $NEW_OWNER_CONTACT_PHONE = "PHONE";
                    $NEW_OWNER_CONTACT_CARGO = "JOB TITLE";
                    $NEW_OWNER_CONTACT_CITY = "CONTACT CITY";
                    $NEW_OWNER_ADDRESS_PRINCIPAL = "PRINCIPAL COMPANY ADDRESS";
                    $NEW_OWNER_ADDRESS_STREET = "STREET ADDRESS";
                    $NEW_OWNER_ADDRESS_CITY = "CITY";
                    $NEW_OWNER_ADDRESS_STATE = "STATE";
                    $NEW_OWNER_ADDRESS_CODE= "ZIP CODE";
                    $NEW_OWNER_ADDRESS_COUNTRY="COUNTRY";
                    $STEP_BACK  ="BACK";
                    $STEP_NEXT = "NEXT";
                    $NEW_OWNER_BOOTH_LOCATION = "PHOTOBOOTH LOCATION";
                    $NEW_OWNER_BOOTH_CORDINATES = "COORDINATES";
                    $NEW_OWNER_BOOTH_LAT = "LATITUDE";
                    $NEW_OWNER_BOOTH_LON = "LONGITUDE";
                    $NEW_OWNER_BUTTON_SAVE = "SAVE";
                    $NEW_OWNER_BOOTH_FRASE1 = "Same Address of Company";
                    
                    $TOSOLD_DISTRIBUTOR = "To change to sold you must assign a distributor:";
                    $TOSOLD_BUTTON = "TO SOLD";
                    $TOSTOCK_BUTTON = "TO STOCK";
                    $CONTROL_QUALITY_FRASE = "Quality control has been passed by:";
                                            $_EXTRA_COMPON_SN = "SERIAL NUMBER";
                        $_EXTRA_COMP_MARK  = "MARK";
                        $_EXTRA_COMP_MODEL = "MODEL";
                        $_EXTRA_COMP_TYPE = "TYPE";
                    
                break;
            case "login":
                $QUESTIONS = "Answer these questions to remove the popup, Thanks";
                $EMAIL_QUESTION = "Your e-mail :";
                
                break;
            case "addresses":
                $_FITXA_OWNER_NAME = "OWNER";
                $_FITXA_ADDRESS = "ADDRESS";
                $_FITXA_CITY = "CITY";
                $_FITXA_STATE = "STATE";
                $_FITXA_CODE = "ZIP CODE";
                $_FITXA_COUNTRY = "COUNTRY";
                $_ADDRESS_TITLE1 = "INFORMATION ADDRESS";
                $_ADDRESS_SHIPPING_PACKS = "ORDERS PACKS PRICE";
                break;
        }
}
?>
