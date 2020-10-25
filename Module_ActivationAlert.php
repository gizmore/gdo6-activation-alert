<?php
namespace GDO\ActivationAlert;

use GDO\Core\GDO_Module;
use GDO\User\GDO_User;
use GDO\Mail\Mail;
use GDO\Net\GDT_IP;
use GDO\Mail\GDT_Email;

/**
 * Sends a mail when a new user is activated.
 * @author gizmore
 */
final class Module_ActivationAlert extends GDO_Module
{
	public function onLoadLanguage() { return $this->loadLanguage('lang/activation_alert'); }
	
	public function getConfig()
	{
		return array(
			GDT_Email::make('activation_alert_mail_receiver')->initial(GWF_ADMIN_EMAIL),
		);
	}
	
	public function cfgMailReceiver() { return $this->getConfigVar('activation_alert_mail_receiver'); }
	
	############
	### Hook ###
	############
	public function hookUserActivated(GDO_User $user)
	{
		$this->sendMails($user);
	}
	
	private function sendMails(GDO_User $user)
	{
		if ($to = $this->cfgMailReceiver())
		{
			$this->sendSingleMail($to, $user);
		}
		else
		{
			foreach (GDO_User::admins() as $admin)
			{
				$this->sendMail($admin, $user);
			}
		}
	}
	
	private function sendSingleMail($to, GDO_User $user)
	{
		$fakeUser = GDO_User::blank(array(
			'user_name' => GWF_BOT_NAME,
			'user_email' => $to,
		));
		$this->sendMail($fakeUser, $user);
	}
	
	private function sendMail(GDO_User $admin, GDO_User $user)
	{
		$mail = Mail::botMail();
		$mail->setSubject(tusr($admin, 'mail_subj_user_activated_staff', [sitename()]));
		$tVars = array(
			$admin->displayNameLabel(),
			sitename(),
			$user->displayNameLabel(),
			GDT_IP::current(),
		);
		$mail->setBody(tusr($admin, 'mail_body_user_activated_staff', $tVars));
		$mail->sendToUser($admin);
	}
	
}

