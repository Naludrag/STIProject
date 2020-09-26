<?php
/**
 * Class dbManager class that contains all the interactions with the database
 */
class dbManager
{
    // Variable that contain the connection to the DB
    private $file_db;

    /**
     * dbManager constructor. When the object is construct will start a connection
     */
    function __construct() {
        $this->file_db = new PDO('sqlite:/usr/share/nginx/databases/database.sqlite');
        // Set errormode to exceptions
        $this->file_db->setAttribute(PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Close the connection with the database
     */
    function closeConnection(){
        $this->file_db = null;
    }

    /**
     * @param $id id of the message who must be found
     * @return false|PDOStatement boolean false it the message isn't found or a an object that represents the message required
     */
    function findMessageByID($id){
        // Search for the desired message
        $stmt = $this->file_db->prepare("SELECT * FROM Messages WHERE id=:id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * @param $id id of the user who must be found
     * @return false|PDOStatement boolean false if the user isn't found or an object that contain the user found
     */
    function findUserByID($id){
        $stmt = $this->file_db->prepare("SELECT * FROM Users WHERE id=:id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * @param $username username of the user who must be found
     * @return false|PDOStatement boolean false if the user isn't found or an object that contain the user found
     */
    function findUserByUsername($username){
        $stmt = $this->file_db->query('SELECT * FROM Users WHERE username=:username');
        $stmt->execute(['username' => $username]);
        return $stmt->fetch();
    }

    /**
     * @param $username username of the user who must be found
     * @param $password password of the user who must be found
     * @return false|PDOStatement boolean false if the user isn't found or an object that contain the user found
     */
    function findUserByUsernamePassword($username, $password){
        $stmt = $this->file_db->query('SELECT * FROM Users WHERE username=:username AND password=:password');
        $stmt->execute(['username' => $username]);
        $stmt->execute(['password' => $password]);
        return $stmt->fetch();
    }

    /**
     * @return false|PDOStatement boolean false if the connection with the DB isn't set
     *                            or an object that contains all the messages
     */
    function findAllMessages(){
        return $this->file_db->query('SELECT m.id, m.date, u.username, m.subject FROM messages AS m INNER JOIN Users AS u ON m.sender == u.id');
    }

    /**
     * @param $subject subject of the message that as to be added
     * @param $body body of the message
     * @param $sender the sender of the message
     * @param $recipient the recipient of the message
     */
    function addMessage($subject, $body, $sender, $recipient){
        // Prepare the date in the right format to write in the database
        $date = new DateTime();
        $date = $date->format('d-m-Y H:i:s');

        // Prepare the database request to insert a new message
        $sql = "INSERT INTO Messages (subject, body, sender, recipient, date) VALUES (:subject, :body, :sender, :recipient, :date)";
        $stmt = $this->file_db->prepare($sql);
        $stmt->bindParam(':subject',$subject);
        $stmt->bindParam(':body',$body);
        $stmt->bindParam(':sender',$sender);
        $stmt->bindParam(':recipient',$recipient);
        $stmt->bindParam(':date',$date);
        $stmt->execute();
    }

    /**
     * @param $id id of the message to delete in the DB
     */
    function deleteMessage($id){
        $stmt = $this->file_db->prepare("DELETE FROM Messages WHERE id=:id");
        $stmt->execute(['id' => $id]);
        $stmt->fetch();
    }
}