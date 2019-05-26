<?php
namespace App\Support;
use PragmaRX\Google2FALaravel\Support\Authenticator;

class Google2FAAuthenticator extends Authenticator{
	protected function canPasswithoutCheckingOTP(){
		if(empty($this->getUser()->passwordSecurity))
			return true;

		return !$this->getUser()->passwordSecurity->google2fa_enable ||
			   !$this->isEnabled() ||
			   $this->noUserIsAuthenticated() ||
			   $this->twoFactorAuthstillValid();

	}

	protected function getGoogle2FASecretKey(){
		$secret = $this->getUser()->passwordSecurity->{$this->config('otp_secret_column')};
		if(is_null($secret) || empty($secret)){
			throw new InvalideSecurityKey('Secret Key Cannot be empty');
		}
		return $secret;
	}
}