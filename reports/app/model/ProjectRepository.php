<?php



class ProjectRepository extends Repository
{
	
	/**
	 * @param string $name
	 * @param int $manday_cost
	 * @return Nette\Database\Table\ActiveRow
	 */
	public function createProject($name, $manday_cost)
	{
		return $this->getTable()->insert(array(
				'name' => $name,
				'manday_cost' => $manday_cost,
		));
	}
	
	
	
	/**
	 * @param int $id
	 * @param string $name
	 * @param int $manday_cost
	 * @return void
	 */
	public function editProject($id, $name, $manday_cost)
	{
		$this->findOneBy(array('project_id' => $id))
		->update(array(
				'name' => $name,
				'manday_cost' => $manday_cost,
		));
	}
	
	
	
	/**
	 * @param int $id
	 * @return void
	 */
	public function deleteProject($id)
	{
		$this->findOneBy(array('user_id' => $id))
		->delete();
	}
	
	
	
	/**
	 * @param int $id
	 * @return \Nette\Database\Table\ActiveRow|FALSE
	 */
	public function getProject($id)
	{
		return $this->find($id);
	}
	
	
	
	/**
	 * @param array $filter
	 * @return Nette\Database\Table\Selection
	 */
	public function getProjects(array $filter = null) {
		if($filter === null) {
			return $this->getTable();
		}
		else {
			return $this->findBy($filter);
		}
	}
	
}