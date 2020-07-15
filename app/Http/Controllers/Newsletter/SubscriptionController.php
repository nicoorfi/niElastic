<?php

declare(strict_types=1);

namespace App\Http\Controllers\Newsletter;

use App\Contracts\MailingList;
use App\Events\NewsletterSubscriptionWasCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNewsletterSubscription;
use App\Models\NewsletterSubscription;
use App\Repositories\NewsletterSubscriptionRepository;
use Inertia\Inertia;

class SubscriptionController extends Controller
{
    private NewsletterSubscriptionRepository $subscriptions;

    public function __construct(NewsletterSubscriptionRepository $newsletterSubscriptionRepository)
    {
        $this->subscriptions = $newsletterSubscriptionRepository;
    }
    /**
     * Store a newly created Newsletter subscription
     * if it doesn't already exist.
     */
    public function store(StoreNewsletterSubscription $request)
    {
        $subscription = $this->subscriptions->firstOrCreate($request->validated());

        event(new NewsletterSubscriptionWasCreated($subscription));

        return redirect()->route('newsletter.thankyou');
    }

    public function thankyou()
    {
        return Inertia::render('newsletter/thankyou');
    }

    public function confirmed()
    {
        return Inertia::render('newsletter/confirmed');
    }
}
