<?php
use PHPUnit\Framework\TestCase;
require './src/Download.php';

class DownloadTest extends  TestCase {

    /**
     * @expectedException Exception
     */
    public function testPrepareDownload()
    {
        $download = new Download();
        $download->remoteFileUrl = 'http://bpic.588ku.com/element_origin_min_pic/18/03/02/c5757c2830b83fe6489f0b0c5a5d7c48.jpg';
        $result = $download->prepareDownload();
        print_r($result);
    }
}