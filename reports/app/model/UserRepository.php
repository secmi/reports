<?php



class UserRepository extends Repository
{
	
	/**
	 * @param string $username
	 * @return Nette\Database\Table\ActiveRow
	 */
	public function createUser($username)
	{
		return $this->getTable()->insert(array(
				'username' => $username,
		));
	}
	
	
	
	/**
	 * @param int $id
	 * @param string $username
	 * @return void
	 */
	public function editUser($id, $username)
	{
		$this->findOneBy(array('user_id' => $id))
		->update(array(
				'username' => $username,
		));
	}
	
	
	
	/**
	 * @param string $id
	 * @param string $password
	 * @return void
	 */
	public function setPassword($id, $password)
	{
		$this->findOneBy(array('user_id' => $id))
		->update(array(
				'passwd' => Authenticator::calculateHash($password)
		));
	}
	
	
	
	/**
	 * @param int $id
	 * @return void
	 */
	public function deleteUser($id)
	{
		$this->find($id)
		->delete();
	}
	
	
	
	/**
	 * @param int $id
	 * @return \Nette\Database\Table\ActiveRow|FALSE
	 */
	public function getUser($id)
	{
		return $this->find($id);
	}
	
	
	
	/**
	 * @param array $filter
	 * @return Nette\Database\Table\Selection
	 */
	public function getUsers(array $filter = null) {
		if($filter === null) {
			return $this->getTable();
		}
		else {
			return $this->findBy($filter);
		}
	}
	
}