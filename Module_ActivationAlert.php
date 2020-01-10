<?php
namespace GDO\ActivationAlert;

use GDO\Core\GDO_Module;
use GDO\User\GDO_User;
use GDO\Mail\Mail;
use GDO\Net\GDT_IP;

final class Module_ActivationAlert extends GDO_Module
{
	public function onLoadLanguage() { return $this->loadLanguage('lang/activation_alert'); }
	
	public function hookUserActivated(GDO_User $user)
	{
		$this->sendMails($user);
	}
	
	private function sendMails(GDO_User $user)
	{
		foreach (GDO_User::admins() as $admin)
		{
			$this->sendMail($admin, $user);
		}
	}
	
	private function sendMail(GDO_User $admin, GDO_User $user)
	{
		$mail = Mail::botMail();
		$mail->setSubject(tusr($admin, 'mail_subj_user_activated_staff', [sitename()]));
		$tVars = array(
			$admin->displayName(),
			sitename(),
			$user->displayName(),
			GDT_IP::current(),
		);
		$mail->setBody(tusr($admin, 'mail_body_user_activated_staff', $tVars));
		$mail->sendToUser($admin);
	}
	
}

