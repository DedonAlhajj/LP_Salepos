<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TenantUserCreated extends Notification
{
    use Queueable;

    protected $userDetails;
    protected $dashboardUrl;

    public function __construct(array $userDetails, string $dashboardUrl)
    {
        $this->userDetails = $userDetails;
        $this->dashboardUrl = $dashboardUrl;
    }

    public function via($notifiable)
    {
        return ['mail','database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your User Account Details')
            ->greeting('Hello ' . $this->userDetails['name'] . ',')
            ->line('Your user account has been created successfully.')
            ->line('Here are your account details:')
            ->line('Email: ' . $this->userDetails['email'])
            ->line('Temporary Password: ' . $this->userDetails['password'])
            ->action('Go to your Dashboard login', $this->dashboardUrl)
            ->line('Please change your password after logging in for the first time.')
            ->line('Thank you for using our platform!');
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'New User Account Created',
            'message' => 'User account for ' . $this->userDetails['name'] . ' has been created successfully.',
            'email' => $this->userDetails['email'],
            'temporary_password' => $this->userDetails['password'],
            'dashboard_url' => $this->dashboardUrl,
        ];
    }
}
