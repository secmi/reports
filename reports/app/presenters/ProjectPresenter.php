<?php

use Nette\Application\UI\Form;


/**
 * Project presenter.
 */
class ProjectPresenter extends BasePresenter
{

	/** @var ProjectRepository */
	private $projectRepository;
	
	/** @var  */
	private $projects;

	

	public function inject(ProjectRepository $projectRepository)
	{
		$this->projectRepository = $projectRepository;
	}

	

	
	public function actionList()
	{
		$this->projects = $this->projectRepository->getProjects();
	}
	
	
	
	public function renderList()
	{
		if($this->projects->count() > 0) 
		{
			$this->template->projects = $this->projects;
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
		$project = $this->projectRepository->getProject($id);
		
		$this['projectForm']->setDefaults(array(
				'name' => $project->name,
				'manday_cost' => $project->manday_cost,
		));
		
		$this['projectForm']->addHidden('project_id', $id);
	}
	
	
	
	public function renderEdit()
	{
		$this->setView('add');
	}
	
	

	/**
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentProjectForm()
	{
		$form = new Form;
		$form->addProtection('Timeout expired, please repeat your last action.');
		
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
				$project_id = $this->projectRepository->createProject(
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