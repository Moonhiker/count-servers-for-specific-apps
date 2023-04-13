<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase;
use CheckServer\Server;

class ServerTest extends TestCase{

    public function testServerWithoutApplications(): void
    {
        $ServerinfoArray = ["CPU_in_GHz" => 8, "Band_in_Gbit" => 200, "Ram_in_GB" => 8, "Name" => "DELL"];

        $Server = new Server($ServerinfoArray, []);
        $this->assertEquals(0, $Server->getUsedServerCount());   
    }

    public function testApplicationWithEmptyParameter(): void
    {
        $App_info = [
            ["CPU_in_GHz" => 0, "Band_in_Gbit" => 0, "Ram_in_GB" => 0, "Name" => "App1"]];

        $ServerinfoArray = ["CPU_in_GHz" => 8, "Band_in_Gbit" => 200, "Ram_in_GB" => 8, "Name" => "DELL"];

        $Server = new Server($ServerinfoArray, $App_info);
        $this->assertEquals(0, $Server->getUsedServerCount());   
    }

    public function testServerWithoutParameter(): void
    {
        $App_info = [
            ["CPU_in_GHz" => 3, "Band_in_Gbit" => 30, "Ram_in_GB" => 1, "Name" => "App1"],
            ["CPU_in_GHz" => 4, "Band_in_Gbit" => 50, "Ram_in_GB" => 2.5, "Name" => "App2"],
            ["CPU_in_GHz" => 5, "Band_in_Gbit" => 75, "Ram_in_GB" => 1.5, "Name" => "App3"]];

        $Server = new Server([], $App_info);
        $this->assertEquals(0, $Server->getUsedServerCount());   
    }
  
    public function testServerWithMissingParameter(): void
    {
        $App_info = [
            ["CPU_in_GHz" => 3, "Band_in_Gbit" => 30, "Ram_in_GB" => 1, "Name" => "App1"],
            ["CPU_in_GHz" => 4, "Band_in_Gbit" => 50, "Ram_in_GB" => 2.5, "Name" => "App2"],
            ["CPU_in_GHz" => 5, "Band_in_Gbit" => 75, "Ram_in_GB" => 1.5, "Name" => "App3"]];

        $ServerinfoArray = ["Band_in_Gbit" => 200, "Ram_in_GB" => 8, "Name" => "DELL"];

        $Server = new Server($ServerinfoArray, $App_info);
        $this->assertEquals(0, $Server->getUsedServerCount());   
    }

    public function testServerWithSmallerParameterThenApplications(): void
    {
        $App_info = [
            ["CPU_in_GHz" => 3, "Band_in_Gbit" => 30, "Ram_in_GB" => 1, "Name" => "App1"],
            ["CPU_in_GHz" => 4, "Band_in_Gbit" => 50, "Ram_in_GB" => 2.5, "Name" => "App2"],
            ["CPU_in_GHz" => 5, "Band_in_Gbit" => 75, "Ram_in_GB" => 1.5, "Name" => "App3"]];

        $ServerinfoArray = ["CPU_in_GHz" => 2, "Band_in_Gbit" => 200, "Ram_in_GB" => 8, "Name" => "DELL"];

        $Server = new Server($ServerinfoArray, $App_info);
        $this->assertEquals(0, $Server->getUsedServerCount());
        $Apps = $Server->getFilledApplications();
        foreach ($Apps as $App)
        {
        $this->assertEquals("Not fit to any server", $App->getServerName());   
        }
    }

    public function testOneApplicationFits(): void
    {
        $App_info = [
            ["CPU_in_GHz" => 3, "Band_in_Gbit" => 30, "Ram_in_GB" => 1, "Name" => "App1"],
            ["CPU_in_GHz" => 4, "Band_in_Gbit" => 50, "Ram_in_GB" => 2.5, "Name" => "App2"],
            ["CPU_in_GHz" => 5, "Band_in_Gbit" => 75, "Ram_in_GB" => 1.5, "Name" => "App3"]];

        $ServerinfoArray = ["CPU_in_GHz" => 3, "Band_in_Gbit" => 200, "Ram_in_GB" => 8, "Name" => "DELL"];

        $Server = new Server($ServerinfoArray, $App_info);
        $Server->firstFitAlgorithm();
        $DellServer = $Server->getUsedServers();
        $this->assertEquals(1, count($DellServer));
        $this->assertSame("App1",$DellServer[0]->getAppsOnServer());
    }

    public function testParatemerAsString(): void
    {
        $App_info = [
            ["CPU_in_GHz" => "3", "Band_in_Gbit" => 30, "Ram_in_GB" => 1, "Name" => "App1"],
            ["CPU_in_GHz" => 4, "Band_in_Gbit" => 50, "Ram_in_GB" => 2.5, "Name" => "App2"],
            ["CPU_in_GHz" => 5, "Band_in_Gbit" => 75, "Ram_in_GB" => "1.5", "Name" => "App3"]];

        $ServerinfoArray = ["CPU_in_GHz" => 3, "Band_in_Gbit" => "200", "Ram_in_GB" => 8, "Name" => "DELL"];

        $Server = new Server($ServerinfoArray, $App_info);
        $Server->firstFitAlgorithm();
        $DellServer = $Server->getUsedServers();
        $this->assertEquals(1, count($DellServer));
        $this->assertSame("App1",$DellServer[0]->getAppsOnServer());
    }

    public function testWrongParatemer(): void
    {
        $App_info = [
            ["CPU_in_GHz" => "abc", "Band_in_Gbit" => 30, "Ram_in_GB" => 1, "Name" => "App1"],
            ["CPU_in_GHz" => 4, "Band_in_Gbit" => 50, "Ram_in_GB" => 2.5, "Name" => "App2"],
            ["CPU_in_GHz" => 5, "Band_in_Gbit" => 75, "Ram_in_GB" => "Empty", "Name" => "App3"]];

        $ServerinfoArray = ["CPU_in_GHz" => 3, "Band_in_Gbit" => "200", "Ram_in_GB" => "Null", "Name" => "DELL"];

        $Server = new Server($ServerinfoArray, $App_info);
        $Server->firstFitAlgorithm();
        $DellServer = $Server->getUsedServers();
        $this->assertEquals(0, count($DellServer));
    }
    
    public function testAllApplicationsFitsIntoOneServer(): void
    {
        $App_info = [
            ["CPU_in_GHz" => 3, "Band_in_Gbit" => 30, "Ram_in_GB" => 1, "Name" => "App1"],
            ["CPU_in_GHz" => 4, "Band_in_Gbit" => 50, "Ram_in_GB" => 2.5, "Name" => "App2"],
            ["CPU_in_GHz" => 4, "Band_in_Gbit" => 50, "Ram_in_GB" => 2.5, "Name" => "App3"],
            ["CPU_in_GHz" => 2, "Band_in_Gbit" => 70, "Ram_in_GB" => 1.5, "Name" => "App4"]];

        $ServerinfoArray = ["CPU_in_GHz" => 16, "Band_in_Gbit" => 200, "Ram_in_GB" => 8, "Name" => "DELL"];

        $Server = new Server($ServerinfoArray, $App_info);
        $Server->firstFitAlgorithm();
        $DellServer = $Server->getUsedServers();
        $this->assertEquals(1, count($DellServer));
        $this->assertSame("App1, App2, App3, App4",$DellServer[0]->getAppsOnServer());
    }

    public function testFiveApplicationsFitsIntoTWoServer(): void
    {
        $App_info = [
            ["CPU_in_GHz" => 3, "Band_in_Gbit" => 30, "Ram_in_GB" => 1, "Name" => "App1"],
            ["CPU_in_GHz" => 1, "Band_in_Gbit" => 50, "Ram_in_GB" => 2.5, "Name" => "App2"],
            ["CPU_in_GHz" => 14, "Band_in_Gbit" => 50, "Ram_in_GB" => 2.5, "Name" => "App3"],
            ["CPU_in_GHz" => 2, "Band_in_Gbit" => 50, "Ram_in_GB" => 2.5, "Name" => "App4"],
            ["CPU_in_GHz" => 2, "Band_in_Gbit" => 70, "Ram_in_GB" => 1.5, "Name" => "App5"]];

        $ServerinfoArray = ["CPU_in_GHz" => 16, "Band_in_Gbit" => 200, "Ram_in_GB" => 8, "Name" => "DELL"];

        $Server = new Server($ServerinfoArray, $App_info);
        $Server->firstFitAlgorithm();
        $DellServer = $Server->getUsedServers();
        $this->assertEquals(2, count($DellServer));
        $this->assertSame("App1, App2, App4, App5",$DellServer[0]->getAppsOnServer());
        $this->assertSame("App3",$DellServer[1]->getAppsOnServer());
    }
}

    


?>