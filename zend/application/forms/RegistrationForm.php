<?php
class Application_Form_RegistrationForm extends Zend_Form
{
	public function init()
	{
		$this->addPrefixPath('App_Form', 'App/Form/');
		
		$firstname = $this->createElement('text','firstname');
		$firstname->setLabel('First Name:')
		->setRequired(false);

		$lastname = $this->createElement('text','lastname');
		$lastname->setLabel('Last Name:')
		->setRequired(false);

		$email = $this->createElement('text','email');
		$email->setLabel('Email: *')
		->setRequired(false);

		$username = $this->createElement('text','username');
		$username->setLabel('Username: *')
		->setRequired(true)->setValidators(array('alpha'));

		$password = $this->createElement('password','password');
		$password->setLabel('Password: *')
		->setRequired(true);
		$genders=array('male'=>'Male','female'=>'Female','others'=>'Transgender');
		$confirmPassword = $this->createElement('password','confirmPassword');
		$confirmPassword->setLabel('Confirm Password: *')
		->setRequired(true);
		$sex=$this->createElement('select', 'gender');
		$sex->setLabel('Gender: *')->setMultiOptions($genders)->setRequired(true);
		
		$dob=$this->createElement('date', 'dob');
		$dob->setLabel('Date of Birth: *')->setRequired(true);
		
		$register = $this->createElement('submit','register');
		$register->setLabel('Sign up')
		->setIgnore(true);

		$this->addElements(array(
				$firstname,
				$lastname,
				$email,
				$username,
				$password,
				$confirmPassword,
				$sex,
				$dob,
				$register
		));
	}
}