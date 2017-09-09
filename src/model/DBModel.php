<?php
include_once("Book.php");


class DBModel 
{        
    /**
      * The PDO object for interfacing the database
      *
      */
    protected $db = null;  
    
    /**
	 * @throws PDOException
     */

    public function __construct($db = null) {  
	    if ($db) {

			$this->db = $db;
		}
		else {   
            try {

                $db = new PDO('mysql:host=localhost;dbname=BookDB;charset=utf8mb4', 'root', 'clank543');
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
                $this->db = $db;      
            } 
            catch (Exception $e) {

                $this-> gracefulErrorPage();
            }      
		}
    }


    private function validInput($book) {
        $isValid = true;

        if ($book-> title == "") {

            $isValid = false;
        }

        return $isValid;
    }


    private function gracefulErrorPage() {

        echo 
            "<h2>An error has occurred.</h2>
                </br>
             <h4>Here, have this graceful error page.</h4>
                </br>
                </br>
             <a href='index.php'> <--- Go back</a>";  
    }

    
    /** Function returning the complete list of books in the collection. Books are
     * returned in order of id.
     * @return Book[] An array of book objects indexed and ordered by their id.
	 * @throws PDOException
     */
    public function getBookList() {

        $booklist = array();

        foreach ($this->db-> query("SELECT * FROM Books") as $row) {
            
            array_push($booklist, new Book($row['title'], $row['author'], $row['description'], $row['id']));
        }

        return $booklist;
    }
    
    /** Function retrieving information about a given book in the collection.
     * @param integer $id the id of the book to be retrieved
     * @return Book|null The book matching the $id exists in the collection; null otherwise.
	 * @throws PDOException
     */
    public function getBookById($id) {
		$book = null;

        foreach ($this->db-> query("SELECT * FROM Books WHERE id=$id") as $row){
            
            $book = new Book($row['title'], $row['author'], $row['description'], $row['id']);
        }

        return $book;
    }
    


    /** Adds a new book to the collection.
     * @param $book Book The book to be added - the id of the book will be set after successful insertion.
	 * @throws PDOException
     */
    public function addBook($book) {
        
        if ($this-> validInput($book) == true) {

            $stmt = $this->db-> prepare("INSERT INTO Books  (title,  author,  description)
                                                     VALUES (?,      ?,       ?)");

            $stmt-> bindValue(1, $book->title);
            $stmt-> bindValue(2, $book->author);
            $stmt-> bindValue(3, $book->description);

            $stmt-> execute();
            return true;
        }
        else {

            $this-> gracefulErrorPage();
            return false;
        }
    }

    /** Modifies data related to a book in the collection.
     * @param $book Book The book data to be kept.
     * @todo Implement function using PDO and a real database.
     */
    public function modifyBook($book) {

        $stmt = $this->db-> prepare("UPDATE Books set title = :title, 
                                                      author = :author, 
                                                      description = :description 
                                                  WHERE id = :id");

        $stmt-> bindValue(':title',         $book->title);
        $stmt-> bindValue(':author',        $book->author);
        $stmt-> bindValue(':description',   $book->description);
        $stmt-> bindValue(':id',            $book->id);

        $stmt-> execute();
    }

    /** Deletes data related to a book from the collection.
     * @param $id integer The id of the book that should be removed from the collection.
     */
    public function deleteBook($id) {

        $stmt = $this->db-> prepare("DELETE FROM Books WHERE id = :id");
        //VALUES (:title, :author, :description, :id)

        $stmt-> bindValue(':id', $id);

        $stmt-> execute();
    }
}

?>