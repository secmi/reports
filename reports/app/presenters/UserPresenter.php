<?php

use Nette\Application\UI\Form;


/**
 * User presenter.
 */
class UserPresenter extends BasePresenter
{

	/** @var UserRepository */
	private $userRepository;
	
	/** @var  */
	private $users;

	

	public function inject(UserRepository $userRepository)
	{
		$this->userRepository = $userRepository;
	}

	

	
	public function actionList()
	{
		$this->users = $this->userRepository->getUsers();
	}
	
	
	
	public function renderList()
	{
		if($this->users->count() > 0) 
		{
			$this->template->users = $this->users;
		}
	}
	
	
	
	public function actionAdd()
	{
		$this['userForm']['password']
			->setRequired('%label field cannot be empty');
		$this['userForm']['confirm_password']
			->setRequired('%label field cannot be empty');
	}
	
	
	
	public function renderAdd()
	{
		
	}
	
	
	
	public function actionEdit($id)
	{
		$user = $this->userRepository->getUser($id);
		
		$this['userForm']->setDefaults(array(
				'username' => $user->username,
		));
		
		$this['userForm']->addHidden('user_id', $id);
	}
	
	
	
	public function renderEdit()
	{
		$this->setView('add');
	}
	
	

	/**
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentUserForm()
	{
		$form = new Form;
		$form->addProtection('Timeout expired, please repeat your last action.');
		
		$form->addText('username', 'username')
			->setRequired('%label field cannot be empty')
			->addRule(Form::MAX_LENGTH, '%label must be maximum %d characters', 20);
		
		$form->addPassword('password', 'password')
			->addCondition(Form::FILLED, TRUE)
				->addRule(Form::MIN_LENGTH, '%label must be at least %d characters.', 6);
		
		$form->addPassword('confirm_password', 'confirm password')
			->addConditionOn($form['password'], Form::FILLED, TRUE)
				->addRule(Form::EQUAL, '%label must be same as new password', $form['password']);
		
		$form->addSubmit('save', 'Save');
		$form->addSubmit('continue', 'Save and new entry');
		
		// call method userFormSucceeded() on success
		$form->onSuccess[] = $this->userFormSucceeded;
		return $form;  		
	}
	
	
	
	/**
	 * @param  Nette\Application\UI\Form $form
	 */
	public function userFormSucceeded(Form $form)
	{
		try {
			$values = $form->getValues();
		
			if(!(isset($values->user_id) && $values->user_id != ''))
			{
				$user_id = $this->userRepository->createUser(
								$values->username
						);
							
				$this->userRepository->setPassword($user_id, $values->password;);				
			}
			else
			{
				$this->userRepository->editUser(
						$values->user_id,
						$values->username
				);
				
				if($values->password != '')
				{ 
					$this->userRepository->setPassword($values->user_id, $values->password);
				}
			}
			
			
			$this->flashMessage('Item successfully saved.', 'success');
			if($form['save']->isSubmittedBy())
			{
				$this->redirect('User:list');
			}
			else
			{
				if (!$this->isAjax())
				{
					$this->redirect('this');
				}
				else
				{
					$this->invalidateControl('userForm');
					$form->setValues(array(), TRUE);
				}
			}
		}
		catch (\PDOException $e)
		{
			$form->addError('Error while saving item, please repeat your last action.' .$e);
			
			if ($this->isAjax())
			{
				$this->invalidateControl('userForm');
			}
		}	
	}

}