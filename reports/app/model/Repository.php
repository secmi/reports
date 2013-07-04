<?php



/**
 * Provádí operace nad databázovou tabulkou.
 */
abstract class Repository extends Nette\Object
{
	/** @var Nette\Database\Connection */
	protected $connection;

	
	
	public function __construct(Nette\Database\Connection $db)
	{
		$this->connection = $db;
	}



	/**
	 * Vrací objekt reprezentující databázovou tabulku.
	 * @return Nette\Database\Table\Selection
	 */
	protected function getTable()
	{
		// název tabulky odvodíme z názvu třídy
		preg_match('#(\w+)Repository$#', get_class($this), $m);
		return $this->connection->table(lcfirst($m[1]));
	}



	/**
	 * Vrací všechny řádky z tabulky.
	 * @return Nette\Database\Table\Selection
	 */
	public function findAll()
	{
		return $this->getTable();
	}



	/**
	 * Vrací řádky podle filtru, např. array('name' => 'John').
	 * @return Nette\Database\Table\Selection
	 */
	public function findBy(array $by)
	{
		return $this->getTable()->where($by);
	}
	
	
	
	/**
	 * To samé jako findBy akorát vrací vždy jen jeden záznam
	 * @param array $by
	 * @return \Nette\Database\Table\ActiveRow|FALSE
	 */
	public function findOneBy(array $by)
	{
		return $this->findBy($by)->limit(1)->fetch();
	}
	
	
	
	/**
	 * Vrací záznam s daným primárním klíčem
	 * @param int $id
	 * @return \Nette\Database\Table\ActiveRow|FALSE
	 */
	public function find($id) 
	{
		return $this->getTable()->get($id);
	}

}