Laravel5 Mail Dispatcher
--------------------------------

## Installation

    {
        "require": {
            ...
            
            "morrelinko/laravel5-mail-dispatcher": "dev-master"
        }
    }
    
## Usage

#### 1. Create a Mail Handler

    namespace App\Mailers\Users;
    
    class SendConfirmation
    {
        protected $user;
        
        public function __construct(User $user)
        {
            $this->user = $user;
        }
        
        public function mail(Mailer $mailer)
        {
            $this->user->confirmation_code = str_random(32);
            $this->user->save();
    
            $mailer->send(
                'emails.confirmation',
                [
                    'user' => $this->user,
                    'code' => $this->user->confirmation_code
                ],
                function ($message) {
                    $message->to($this->user->email)
                        ->subject(sprintf('%s %s', config('app.title'), 'Email Verification'));
                }
            );
        }
    }
    
Note: You can type hint `mail()` with Objects you want resolved out of the Container (As is `Mailer` in the example). 
    
#### 2. Just add the `SendsMail` trait to any class you want to dispatch a mail like so:

    use Morrelinko\MailDispatcher\SendsMail;
    
    class UserAuthController
    {
        use SendsMail;
        
        public function register()
        {
            $user = User::whereId(1)->first();
            
            $this->mail(
                new SendConfirmation($user)
            );
        }
    }


