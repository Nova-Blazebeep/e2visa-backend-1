<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactRequest;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function store(ContactRequest $request)
    {
        try {
            $validated = $request->validated();

            $page = Contact::create($validated);

            return makeResponse(SUCCESS_CODE, "Thank you! Your message has been sent successfully. We'll get back to you shortly.", $page);
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }
}
