Laravel5 Mail Dispatcher
--------------------------------

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


