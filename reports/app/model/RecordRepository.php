<?php



class RecordRepository extends Repository
{
	
	/**
	 * @param int $user_id
	 * @param int $project_id
	 * @param datetime $from_datetime
	 * @param datetime $to_datetime
	 * @param string $note
	 * @param string $bug_note
	 * @return Nette\Database\Table\ActiveRow
	 */
	public function createRecord($user_id, $project_id, $from_datetime, $to_datetime, $note, $bug_note)
	{
		return $this->getTable()->insert(array(
				'user_id' => $user_id,
				'project_id' => $project_id,
				'from_datetime' => $from_datetime,
				'to_datetime' => $to_datetime,
				'note' => $note,
				'bug_note' => $bug_note,
		));
	}
	
	
	
	/**
	 * @param int $id
	 * @param int $user_id
	 * @param int $project_id
	 * @param datetime $from_datetime
	 * @param datetime $to_datetime
	 * @param string $note
	 * @param string $bug_note
	 * @return void
	 */
	public function editRecord($id, $user_id, $project_id, $from_datetime, $to_datetime, $note, $bug_note)
	{
		$this->findOneBy(array('record_id' => $id))
		->update(array(
				'user_id' => $user_id,
				'project_id' => $project_id,
				'from_datetime' => $from_datetime,
				'to_datetime' => $to_datetime,
				'note' => $note,
				'bug_note' => $bug_note,
		));
	}
	
	
	
	/**
	 * @param int $id
	 * @return void
	 */
	public function deleteRecord($id)
	{
		$this->findOneBy(array('user_id' => $id))
		->delete();
	}
	
	
	
	/**
	 * @param int $id
	 * @return \Nette\Database\Table\ActiveRow|FALSE
	 */
	public function getRecord($id)
	{
		return $this->find($id);
	}
	
	
	
	/**
	 * @param array $filter
	 * @return Nette\Database\Table\Selection
	 */
	public function getRecords(array $filter = null) {
		if($filter === null) {
			return $this->getTable();
		}
		else {
			return $this->findBy($filter);
		}
	}
	
}