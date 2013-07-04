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
	private $recordRepository;
	
	/** @var UserRepository */
	private $recordRepository;
	
	/** @var  */
	private $records;

	

	public function inject(RecordRepository $recordRepository)
	{
		$this->recordRepository = $recordRepository;
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
				'user_id' => $project->user_id,
				'project_id' => $project->project_id,
				'from_datetime' => $project->from_datetime,
				'to_datetime' => $project->to_datetime,
				'note' => $project->note,
				'bug_note' => $project->bug_note,
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
		$categories = $this->categoryRepository->getCategories()->fetchPairs('category_id', 'name');
		$categories = $this->categoryRepository->getCategories()->fetchPairs('category_id', 'name');
		
		$form = new Form;
		$form->addProtection('Timeout expired, please repeat your last action.');
		
		$form->addSelect('category_id', 'kategorie', $categories)
		->setRequired('Prosím, vyplňte pole %label.')
		->setPrompt('- Vyberte -');
		
		$form->addText('name', 'name')
			->setRequired('%label field cannot be empty')
			->addRule(Form::MAX_LENGTH, '%label must be maximum %d characters', 100);
		
		$form->addText('manday_cost', 'manday cost')
			->setRequired('%label field cannot be empty')
			->addRule(Form::INTEGER, '%label must be an integer');
		
		$form->addSubmit('save', 'Save');
		$form->addSubmit('continue', 'Save and new entry');
		
		// call method projectFormSucceeded() on success
		$form->onSuccess[] = $this->projectFormSucceeded;
		return $form;  		
	}
	
	
	
	/**
	 * @param  Nette\Application\UI\Form $form
	 */
	public function projectFormSucceeded(Form $form)
	{
		try {
			$values = $form->getValues();
		
			if(!(isset($values->project_id) && $values->project_id != ''))
			{
				$user_id = $this->projectRepository->createProject(
								$values->name,
								$values->manday_cost
						);			
			}
			else
			{
				$this->projectRepository->editProject(
						$values->project_id,
						$values->name,
						$values->manday_cost
				);
			}
			
			
			$this->flashMessage('Item successfully saved.', 'success');
			if($form['save']->isSubmittedBy())
			{
				$this->redirect('Project:list');
			}
			else
			{
				if (!$this->isAjax())
				{
					$this->redirect('this');
				}
				else
				{
					$this->invalidateControl('projectForm');
					$form->setValues(array(), TRUE);
				}
			}
		}
		catch (\PDOException $e)
		{
			$form->addError('Error while saving item, please repeat your last action.' .$e);
			
			if ($this->isAjax())
			{
				$this->invalidateControl('projectForm');
			}
		}	
	}

}