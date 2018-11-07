    /**
     * Connect to FTP client and download data
     * @param $serverFile
     * @return string
     */
    public function getData($serverFile){

        $ftpServer = env('FTP_SERVER');
        $ftpUsername = env('FTP_USERNAME');
        $ftpPassword = env('FTP_PASSWORD');

        $conn_id = ftp_connect($ftpServer);

        try {
            $login_result = ftp_login($conn_id, $ftpUsername, $ftpPassword);
        } catch(\Exception $e){
            return $e->getMessage();
            $log->save();$log = new Log();
            $log->message = "Could not establish a connection to ftp server $ftpServer";
            $log->save();

        }

        $localFile = "storage/app/data.zip";

        // Attempt download of data file
        if(ftp_get($conn_id, $localFile, $serverFile, FTP_BINARY)){
            echo "File transfer successful - $localFile\n";

            $this->unzipFile();
        }else{
            return "There was an error while downloading $serverFile";
            $log = new Log();
            $log->message = "There was an error while downloading $serverFile";
        }

    }

    /**
     * Unzip downloaded file
     * @return string
     */
    public function unzipFile(){

        $filepath = 'storage/app/';
        $filename = 'data.zip';

        $zip = new ZipArchive;
        $res = $zip->open($filepath . $filename);
        if ($res === TRUE) {
            try {
                $zip->extractTo($filepath . 'ftp/');
                $zip->close();
                echo "Extraction of zip successful!\n";
            } catch(\Exception $e){
                $log = new Log();
                $log->message = "Error extracting zip $filename : zip not extracted.";
                $log->save();

                return ($e->getMessage());
            }
        } else {
            $log = new Log();
            $log->message = "Error opening zip $filename.";
            $log->save();
        }
    }