<?php
use PHPUnit\Framework\TestCase;
require './src/Download.php';

class DownloadTest extends  TestCase
{
    protected $isSkipTest = true;
    protected $downloadObj;

    protected function setUp()
    {
        $this->downloadObj = new Download();
        $this->downloadObj->remoteFileUrl = 'http://bpic.588ku.com/element_origin_min_pic/18/03/02/c5757c2830b83fe6489f0b0c5a5d7c48.jpg';

    }

    /**
     * @return mixed
     */
    public function testPrepareDownload()
    {
        $result = $this->downloadObj->prepareDownload();
        $this->assertNotEmpty($result);
        return $result;
    }


    /**
     * @depends testPrepareDownload
     * @param array $file_arr
     */
    public function testStartDownload(array $file_arr)
    {
        $this->assertNotEmpty($file_arr);
        $this->downloadObj->tmpFile = $file_arr['tmp_file'];
        $this->downloadObj->startDownload();
        $this->assertFileExists($this->downloadObj->tmpPath.$file_arr['tmp_file']);
    }

    /**
     * @depends testPrepareDownload
     * @param array $file_arr
     */
    public function testGetTmpFileSize(array $file_arr)
    {
        $this->assertNotEmpty($file_arr);
        $this->downloadObj->tmpFile = $file_arr['tmp_file'];
        $result = $this->downloadObj->getTmpFileSize();
        print_r($result);
    }

}