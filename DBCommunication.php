<?php
require "constants.php";
class DBCommunication{
    protected $connection;

    public function __construct($password = DB_PASSWORD, $database = DB_NAME, $host = DB_HOST, $login = DB_USER)
    {
        $this->connection = new mysqli($host, $login, $password, $database);
        if($this->connection->connect_errno)
        {
            //$err = "Troubles were occurred<br/>";
            $err = "Code error - " . $this->connection->connect_errno . "<br/>";
            $err .= "Description - " . $this->connection->connect_error . "<br/>";
            die($err);
        }
    }

    //return true if email was added
    //       false if email already exist in table or an error was occurred
    public function add_email($email)
    {
        if ($this->check_for_existing("Mailing", "email", $email) == 0) {
            $email = $this->connection->real_escape_string($email);
            $result = $this->send_sql_request("INSERT INTO `Mailing` (`email`) VALUES ('" . $email . "');");
            return true;
        }
        else
            return false;
    }

    //check for email already existence in a table
    private function check_for_existing($table, $column_name ,$value)
    {
        $result = $this->send_sql_request("SELECT `userId` FROM `" . $table. "` WHERE `" . $column_name . "` LIKE '" . $value . "';");

        $amount = $result->num_rows;
        $result->close();
        return  $amount;
    }

    //get user password from database using an email as identifying key
    public function get_user_password($email, $table = "LogPas")
    {
        $email = $this->connection->real_escape_string($email);

        $result = $this->send_sql_request("SELECT `userId`, `password` FROM `" . $table . "` WHERE `email` = '" . $email . "';");

            if ($result->num_rows == 1) {
                return $result->fetch_assoc();
            } else {
                return "No user with such email.";
            }
    }

    //get all info about a user stored in
    public function get_user_info($id, $table = "LogPas")
    {
        $id = $this->connection->real_escape_string($id);

        $result = $this->send_sql_request("SELECT `userId`, `email`, `name`, `surname`, `username` FROM `" . $table . "` WHERE `userId` = '" . $id . "';");

        if ($result->num_rows == 1) {
            return $result->fetch_assoc();
        } else {
            return "No user with such email.";
        }
    }


    //return an array of arrays than consist all data from given table
    public function get_data_from_table($table)
    {
        $table = $this->connection->real_escape_string($table);

        $result = $this->send_sql_request("SELECT * FROM `". $table . "`;");

        $output_array = array();

        if ($result->num_rows > 0){
            while ($row = $result->fetch_assoc()) {
                array_push($output_array, $row);
            }
        }

        return $output_array;
    }

    //add posipaka bot user to database
    public function add_user($email, $username, $password, $name, $surname)
    {
        $email_amount = $this->check_for_existing("LogPas", "email", $email);
        $username_amount = $this->check_for_existing("LogPas", "username", $username);

        $username = $this->connection->real_escape_string($username);
        $password = $this->connection->real_escape_string($password);
        $name = $this->connection->real_escape_string($name);
        $surname = $this->connection->real_escape_string($surname);
        $email = $this->connection->real_escape_string($email);

        $value = "'" . $email . "', '" . $password . "', '" . $name . "', '" . $surname . "', '" . $username . "'";
        $column = "`email`, `password`, `name`, `surname`, `username`";

        if ($email_amount == 0 && $username_amount == 0) {
            $result = $this->send_sql_request("INSERT INTO `LogPas` (" . $column .") VALUES (" . $value . ");");
            $this->add_email($email);
            
            return true;
        }
        elseif ($email_amount != 0)
            return "User with such email is already registered.";
        elseif ($username_amount != 0)
            return "User with such username is already registered.";
        else
            return false;
    }

    public function get_user_id($username, $table)
    {
        $username = $this->connection->real_escape_string($username);
        $table = $this->connection->real_escape_string($table);

        $results = $this->send_sql_request("SELECT `userId` FROM `" . $table. "` WHERE `username` = '" . $username. "';");
        return $results->fetch_assoc();
    }

    //send specified request to DB, obtain request error, return to '$result' respond
    protected function send_sql_request($request)
    {
        if (!$result = $this->connection->query($request)) {
            //echo "Troubles comes during SQL request.<br/>";
            return "Error code " . $this->connection->connect_errno . "<br/>"."Description " . $this->connection->connect_error . "<br/>";
        }

        return $result;
    }

    public function __destruct()
    {
        $this->connection->close();
    }
}
?>
