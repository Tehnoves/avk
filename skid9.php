<?php
/*
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
*/

//$dataString = "-0123$-0124$+0125$+0126$-0127$-0223$-0224$+0225$+0226$-0227$-0323$-0324";
$dataString = $_GET['key1'];
$date = date("Y-m-d H:i:s");

writeData($dataString, $date); //запись в БД

//POST($dataString, $date, '134.249.188.218/interfacing/client_skid9.php');//отправка данных пост запросом //строка ассоциативного массива
POST($dataString, $date, '10.3.11.58/client_skid9.php');//отправка данных пост запросом //строка ассоциативного массива



/**********************************************************************************************************************/
//создается подключение к базе данных
function connection(): \PDO
{
    $dsn = 'mysql:dbname=kip;host=localhost';
    $user = 'root';
    $password = 'kip';

    try {
        $PDO = new PDO($dsn, $user, $password);
        $PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $PDO->exec("SET NAMES utf8");
        $PDO->exec("SET character set utf8");
        $PDO->exec("SET character_set_connection='utf8'");

        return $PDO;
    } catch (\PDOException $e) {
        echo $e->getMessage() . "\n";
        echo $e->getLine();

        return $PDO = null;
    }
}

//записываются данные в таблицу
function writeData(string $dataString, string $date)
{
    $dataArray = explode('$', $dataString);

    $sql = 'INSERT INTO skid9 (date, k1, k2, k3, k4, k5, t_var, ves_var, b1, b2, b3, speed, string)
        VALUES (:date, :k1, :k2, :k3, :k4, :k5, :t_var, :ves_var, :b1, :b2, :b3, :speed, :string)';

    try {
        $PDO = connection(); // подключение к БД
        $payments = $PDO->prepare($sql);

        $payments->bindParam(':date', $date, PDO::PARAM_STR);
        $payments->bindParam(':k1',  $dataArray[0], PDO::PARAM_INT);
        $payments->bindParam(':k2',  $dataArray[1], PDO::PARAM_INT);
        $payments->bindParam(':k3',  $dataArray[2], PDO::PARAM_INT);
        $payments->bindParam(':k4',  $dataArray[3], PDO::PARAM_INT);
        $payments->bindParam(':k5',  $dataArray[4], PDO::PARAM_INT);
        $payments->bindParam(':t_var',  $dataArray[5], PDO::PARAM_INT);
        $payments->bindParam(':ves_var',  $dataArray[6], PDO::PARAM_INT);
        $payments->bindParam(':b1',  $dataArray[7], PDO::PARAM_INT);
        $payments->bindParam(':b2',  $dataArray[8], PDO::PARAM_INT);
        $payments->bindParam(':b3',  $dataArray[9], PDO::PARAM_INT);
        $payments->bindParam(':speed',  $dataArray[10], PDO::PARAM_INT);
        $payments->bindParam(':string',  $dataString, PDO::PARAM_STR);

        $payments->execute();
    } catch (\PDOException $e) {
        echo $e->getMessage() . "\n";
        echo $e->getLine();
        $firstPay = null;
    }
}

//отправка POST запроса на другой сервер
function POST(string $dataString, string $date, string $url)
{
    $dataForTransfer = ['dataString'=>$dataString, 'date'=>$date];

    try {
        $stream = curl_init();
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [],
            CURLOPT_POSTFIELDS => $dataForTransfer
        ];

        curl_setopt_array($stream, $options);
        $postResponse = curl_exec($stream);
    } catch (Exception $e) {
        throw new Exception(curl_error($stream));
    }

    curl_close($stream);
}