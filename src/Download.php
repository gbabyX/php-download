<?php

class Download
{
    public $remoteFileUrl;
    public $fileExt;
    private $tmpPath = __DIR__.'/Uploads_cache/';
    private $exceptionMap = array(
        1001 => 'make dir error',
        1002 => 'remote file error',
        1003 => 'cache file error',
        1004 => 'cache file not exists'
    );

    /**
     *  下载
     * @param $tmp_file
     * @throws Exception
     */
    public function startDownload($tmp_file)
    {
        set_time_limit(0);
        touch($tmp_file);
        // 做些日志处理
        if ($fp = fopen($this->remoteFileUrl, "rb")) {
            if (!$download_fp = fopen($tmp_file, "wb")) {
                throw new Exception($this->exceptionMap[1003],1003);
            }
            while (!feof($fp)) {
                if (!file_exists($tmp_file)) {
                    // 如果临时文件被删除就取消下载
                    fclose($download_fp);
                    throw new Exception($this->exceptionMap[1004]);
                }
                fwrite($download_fp, fread($fp, 1024 * 8 ), 1024 * 8);
            }
            fclose($download_fp);
            fclose($fp);
        } else {
            throw new Exception($this->exceptionMap[1002],1002);
        }

    }

    /**
     * 预下载文件
     */
    public function prepareDownload()
    {
        if (!is_dir($this->tmpPath)){
            if (false === mkdir($this->tmpPath)){
                throw new Exception($this->exceptionMap[1001],1001);
            }
        }
        if (empty($this->remoteFileUrl)){
            throw new Exception($this->exceptionMap[1002],1002);
        }
        if (empty($this->fileExt)){
            $path_info = pathinfo($this->remoteFileUrl);
            $this->fileExt = $path_info['extension'];
        }
        $tmp_name = $this->tmpPath.$this->setTmpFileName($this->fileExt);
        $headers = get_headers($this->remoteFileUrl,true);
        return ['tmp_file' => $tmp_name,'size' => $headers['Content-Length']];
    }

    /**
     *  临时存储远程文件
     * @param $remote_url
     * @param $tmp_path
     */
    private function storeRemoteFile($remote_url,$tmp_path)
    {
        $fp_out = fopen($tmp_path,'w');
        $ch = curl_init($remote_url);
        curl_setopt($ch,CURLOPT_FILE,$fp_out);
        curl_exec($ch);
        curl_close($ch);
    }

    /**
     *  设置文件临时存储名称
     * @param string $ext
     * @return string
     */
    private function setTmpFileName($ext = 'zip')
    {
        return sprintf('%s-%d.%s',uniqid(),rand(10000,99999),$ext);
    }
}