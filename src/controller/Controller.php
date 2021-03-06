<?php
//include_once("model/Model.php");
include_once("model/DBModel.php");
include_once("model/Book.php");
include_once("view/BookListView.php");
include_once("view/BookView.php");
include_once("view/ErrorView.php");

/** The Controller is responsible for handling user requests, for exchanging data with the Model,
 * and for passing user response data to the various Views. 
 * @author Rune Hjelsvold
 * @see model/Model.php The Model class holding book data.
 * @see view/viewbook.php The View class displaying information about one book.
 * @see view/booklist.php The View class displaying information about all books.
 * @see http://php-html.net/tutorials/model-view-controller-in-php/ The tutorial code used as basis.
 */
class Controller {
	public $db_model;
	
	public static $OP_PARAM_NAME = 'op';
	public static $DEL_OP_NAME = 'del';
	public static $ADD_OP_NAME = 'add';
	public static $MOD_OP_NAME = 'mod';
	
	public function __construct()  
    {  
		session_start();
        $this->db_model = new DBModel();
    } 
	
/** The one function running the controller code.
 */
	public function invoke()
	{
		if (isset($_GET['id']))
		{
			// show the requested book
			$book = $this->db_model->getBookById($_GET['id']);
			if ($book)
			{
				$view = new BookView($book, self::$OP_PARAM_NAME, self::$DEL_OP_NAME, self::$MOD_OP_NAME);
				$view->create();
			}
			else
			{
				$view = new ErrorView();
				$view->create();
			}
		}
		else 
		{
			if (isset($_POST[self::$OP_PARAM_NAME]))//A book record is to be added, deleted, or modified
			{
				switch($_POST[self::$OP_PARAM_NAME]) 
				{

				case self::$ADD_OP_NAME : 
				    $book = new Book($_POST['title'], $_POST['author'], $_POST['description']);
				    if($this->db_model->addBook($book) == true)
				    {
				    	$this-> printBookList();
				    }
				    break;


				case self::$DEL_OP_NAME : 
				    $this->db_model->deleteBook($_POST['id']);
				    $this-> printBookList();
				    break;


				case self::$MOD_OP_NAME : 
				    $book = new Book($_POST['title'], $_POST['author'], $_POST['description'], $_POST['id']);
				    if($this->db_model->modifyBook($book))
				    {
				    	$this-> printBookList();
				    }
				    break;	

				}
			}
			else
			{
				$this-> printBookList();	// no special book is requested, we'll show a list of all available books


			}
		}
	}

	private function printBookList()
	{
			$books = $this->db_model->getBookList();
			$view = new BookListView($books, self::$OP_PARAM_NAME, self::$ADD_OP_NAME);
			$view->create();
	}
}

?>