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

	

	public function inject(RecordRepository $recordRepository, UserRepository $userRepository, ProjectRepository $projectRepository)
	{
		$this->recordRepository = $recordRepository;
		$this->userRepository = $userRepository;
		$this->projectRepository = $projectRepository;
	}

	

	
	public function renderSummary()
	{
		
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