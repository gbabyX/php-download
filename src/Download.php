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
        1004 => 'cache file not exists',
        1005 => 'attribute tmpFile is empty'
    );
    public $tmpFile;

    /**
     *  下载
     * @throws Exception
     */
    public function startDownload()
    {
        $tmp_file = $this->getTmpPath();
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
        $tmp_name = $this->setTmpFileName($this->fileExt);
        $headers = get_headers($this->remoteFileUrl,true);
        return ['tmp_file' => $tmp_name,'size' => $headers['Content-Length']];
    }

    /**
     * 获取缓存文件大小
     */
    public function getTmpFileSize()
    {
        $tmp_file = $this->getTmpPath();
        return ['size' => filesize($tmp_file)];
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

    /**
     *  获取 缓存路径
     * @return string
     * @throws Exception
     */
    private function getTmpPath()
    {
        if (empty($this->tmpFile)){
            throw new Exception($this->exceptionMap[1005]);
        }
        return $this->tmpPath.$this->tmpFile;
    }

    public function __get($name)
    {
        return $this->$name;
    }
}