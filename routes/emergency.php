<?php

// Emergency logout function for navigation issues
Route::get('/emergency-logout', function() {
    // Force logout the user
    Auth::logout();
    
    // Clear all session data
    Session::flush();
    
    // Regenerate the session ID
    Session::regenerate();
    
    // Redirect to login page
    return redirect('/login')->with('status', 'You have been logged out due to an emergency logout request.');
})->name('emergency-logout');
