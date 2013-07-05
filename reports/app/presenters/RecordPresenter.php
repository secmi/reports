<?php

use Nette\Application\UI\Form;


/**
 * Record presenter.
 */
class RecordPresenter extends BasePresenter
{

	/** @var RecordRepository */
	private $recordRepository;

	/** @var UserRepository */
	private $userRepository;
	
	/** @var ProjectRepository */
	private $projectRepository;
	
	/** @var  */
	private $records;
	
	/** @var  */
	private $summary;
	
	/** @var  string */
	private $type;
	
	/** @persistent string */
	public $prev_sort = NULL;

	

	public function inject(RecordRepository $recordRepository, UserRepository $userRepository, ProjectRepository $projectRepository)
	{
		$this->recordRepository = $recordRepository;
		$this->userRepository = $userRepository;
		$this->projectRepository = $projectRepository;
	}

	
	/*
	* @param string $sort
	* @param string $type
	*/
	public function actionSummary($sort = NULL, $type = 'ASC')
	{
		$filter = $this->getSession('filter');
		
		$this['recordFilterForm']->setDefaults(array(
				'user_id' => $filter->user_id,
				'project_id' => $filter->project_id,
		));
		
		$data_filter = array();
		if($filter->user_id != '') $data_filter['user.user_id'] = $filter->user_id;
		if($filter->project_id != '') $data_filter['project.project_id'] = $filter->project_id;
		
		$this->type = ($sort != $this->prev_sort) ? 'ASC' : $type;
			
		$this->records = $this->recordRepository->getRecords($data_filter);
		if($sort)
		{
			$order = $sort . ' ' . $this->type;
			$this->prev_sort = $sort;
			
			$this->records->order($order);
		}
		
		$this->summary = $this->recordRepository->getSummary($data_filter);
	}
	

	
	public function renderSummary()
	{
		if($this->records->count() > 0)
		{
			$this->template->records = $this->records;
			$this->template->summary = $this->summary;
			$this->template->type = ($this->type == 'ASC') ? 'DESC' : 'ASC';
		}
	}
	
	
	
	public function actionList()
	{
		$this->records = $this->recordRepository->getRecords();
	}
	
	
	
	public function renderList()
	{
		if($this->records->count() > 0) 
		{
			$this->template->records = $this->records;
		}
	}
	
	
	
	public function actionAdd()
	{

	}
	
	
	
	public function renderAdd()
	{
		
	}
	
	
	
	public function actionEdit($id)
	{
		$record = $this->recordRepository->getRecord($id);
		
		$this['recordForm']->setDefaults(array(
				'user_id' => $record->user_id,
				'project_id' => $record->project_id,
				'from_datetime' => $record->from_datetime,
				'to_datetime' => $record->to_datetime,
				'note' => $record->note,
				'bug_note' => $record->bug_note,
		));
		
		$this['recordForm']->addHidden('record_id', $id);
	}
	
	
	
	public function renderEdit()
	{
		$this->setView('add');
	}
	
	
	
	protected function createComponentQuestionWindow()
	{
		return new QuestionWindowControl($this->getSession('questionWindow'), array($this, 'deleteItem'));
	}
	
	
	
	/**
	 * @param int $id
	 */
	function deleteItem($id)
	{
		try {
			$this->recordRepository->deleteRecord($id);
	
			$this->flashMessage('Item successfully deleted.', 'success');
			$this->redirect('Record:list');
		}
		catch (PDOException $e) {
			$this->flashMessage('Error while saving item, please repeat your last action.', 'error');
			$this->redirect('Record:list');
		}
	}
	
	
	/**
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentRecordFilterForm()
	{
		$users = $this->userRepository->getUsers()->fetchPairs('user_id', 'username');
		$projects = $this->projectRepository->getProjects()->fetchPairs('project_id', 'name');
	
		$form = new Form;
		$form->addProtection('Timeout expired, please repeat your last action.');
	
		$form->addSelect('user_id', 'user', $users)
			->setPrompt('- Select -');
	
		$form->addSelect('project_id', 'project', $projects)
			->setPrompt('- Select -');

		// call method recordFormSucceeded() on success
		$form->onSuccess[] = $this->recordFilterFormSucceeded;
		return $form;
	}
	
	
	
	/**
	 * @param  Nette\Application\UI\Form $form
	 */
	public function recordFilterFormSucceeded(Form $form)
	{
		$values = $form->getValues();
		
		$filter = $this->getSession('filter');
				
		$filter->user_id = $values->user_id;
		$filter->project_id = $values->project_id;	
		
		$this->redirect('this');
	}
	
	

	/**
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentRecordForm()
	{
		$users = $this->userRepository->getUsers()->fetchPairs('user_id', 'username');
		$projects = $this->projectRepository->getProjects()->fetchPairs('project_id', 'name');
		
		$form = new Form;
		$form->addProtection('Timeout expired, please repeat your last action.');
		
		$form->addSelect('user_id', 'user', $users)
			->setRequired('%label field cannot be empty')
			->setPrompt('- Select -');

		$form->addSelect('project_id', 'project', $projects)
			->setRequired('%label field cannot be empty')
			->setPrompt('- Select -');
		
		$form->addText('from_datetime', 'from datetime')
			->setRequired('%label field cannot be empty');
		
		$form->addText('to_datetime', 'to datetime')
		->setRequired('%label field cannot be empty');
		
		$form->addTextArea('note', 'note')
			->addCondition(Form::FILLED, TRUE)
				->addRule(Form::MAX_LENGTH, '%label must be maximum %d characters', 255);
		
		$form->addTextArea('bug_note', 'bug note')
			->addCondition(Form::FILLED, TRUE)
				->addRule(Form::MAX_LENGTH, '%label must be maximum %d characters', 255);
		
		$form->addSubmit('save', 'Save');
		$form->addSubmit('continue', 'Save and new entry');
		
		// call method recordFormSucceeded() on success
		$form->onSuccess[] = $this->recordFormSucceeded;
		return $form;  		
	}
	
	
	
	/**
	 * @param  Nette\Application\UI\Form $form
	 */
	public function recordFormSucceeded(Form $form)
	{
		try {
			$values = $form->getValues();
		
			if(!(isset($values->record_id) && $values->record_id != ''))
			{
				$record_id = $this->recordRepository->createRecord(
							$values->user_id,
							$values->project_id,
							$values->from_datetime,
							$values->to_datetime,
							$values->note,
							$values->bug_note
						);			
			}
			else
			{
				$this->recordRepository->editRecord(
						$values->record_id,
						$values->user_id,
						$values->project_id,
						$values->from_datetime,
						$values->to_datetime,
						$values->note,
						$values->bug_note
				);
			}
			
			
			$this->flashMessage('Item successfully saved.', 'success');
			if($form['save']->isSubmittedBy())
			{
				$this->redirect('Record:list');
			}
			else
			{
				if (!$this->isAjax())
				{
					$this->redirect('this');
				}
				else
				{
					$this->invalidateControl('recordForm');
					$form->setValues(array(), TRUE);
				}
			}
		}
		catch (\PDOException $e)
		{
			$form->addError('Error while saving item, please repeat your last action.' .$e);
			
			if ($this->isAjax())
			{
				$this->invalidateControl('recordForm');
			}
		}	
	}

}