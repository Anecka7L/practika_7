<?php
    require_once("../../settings/connect_datebase.php");
    require_once("../../settings/log_functions.php");

    $Sql = "SELECT * FROM `logs` ORDER BY `Date`";
    $Query = $mysqli->query(query: $Sql);

    $Events = array();

    while($Read = $Query->fetch_assoc()){
        $Status = "";

		$SqlSession = "SELECT * FROM `session` WHERE `IdUser` = 
        {$Read["IdUser"]} ORDER BY `DateStart` DESC";
			$QuerySession = $mysqli->query(query: $SqlSession);
			if($QuerySession->num_rows > 0){
			$ReadSession = $QuerySession->fetch_assoc();
							

			$TimeEnd = strtotime(datetime: $ReadSession["DateNow"]) + 5*60;
			$TimeNow = time();

            if($TimeEnd > $TimeNow)
                $Status = "online";
            else{
                 $TimeEnd = strtotime(datetime: $ReadSession["DateNow"]);
                $TimeDelta = round(num: ($TimeNow - $TimeEnd)/60);
                $Status = "Был в сети: {$TimeDelta} минут назад";
                }			
	}
					

        $Event = array(
            "Id" => $Read["Id"],
            "Ip" => $Read["Ip"],
            "Date" => $Read["Date"],
            "TimeOnline" => $Read["TimeOnline"],
            "Status" => $Status,
            "Event" => $Read["Event"]
        );
        array_push($Events, $Event);
    }
    logToFile("Запрошен журнал событий. Записей: " . count($Events));
    echo json_encode(value: $Events, flags: JSON_UNESCAPED_UNICODE);
?>