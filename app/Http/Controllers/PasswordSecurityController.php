<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Hash;
use App\PasswordSecurity;

class PasswordSecurityController extends Controller
{
  public function show2faForm(Request $request){
  	$user = Auth::user();
  	$google2fa_image = '';
  	if($user->passwordSecurity()->exists()){
  		$google2fa = app('pragmarx.google2fa');
  		$google2fa_url = $google2fa->getQRCodeUrl(
  			'Perfect Web Solutions',
  			$user->email,
  			$user->passwordSecurity->google2fa_secret,
  		);

  		$renderer = new \BaconQrCode\Renderer\Image\Png();
		  $renderer->setHeight(256);
		  $renderer->setWidth(256);
		  $writer = new \BaconQrCode\Writer($renderer);
		  $google2fa_image = base64_encode($writer->writeString($google2fa_url));
  	}
  	$data = array(
  		'user' => $user,
  		'google2fa_image' => $google2fa_image, 
  	);
  	return view('auth.2fa')->with('data' , $data);
  }
  public function generate2fasecret(Request $request){
  	$user = Auth::user();
  	$google2fa = app('pragmarx.google2fa');
  	PasswordSecurity::create([
  		'user_id'          => $user->id,
  		'google2fa_enable' => 0,
  		'google2fa_secret' => $google2fa->generateSecretKey(),
  	]);
  	return redirect('/2fa')->with('success', "Secret Key Generated Successfully!");
  }
  public function enable2fa(Request $request){
  	$user = Auth::user();
  	$google2fa = app('pragmarx.google2fa');
  	$secret = $request->input('verify-code');
  	$valid = $google2fa->verifyKey($user->passwordSecurity->google2fa_secret, $secret);
  	if($valid){
  		$user->passwordSecurity->google2fa_enable = 1;
  		$user->passwordSecurity->save();
  		return redirect('2fa')->with('success', '2FA Enabled Successfully');
  	}else{
  		return redirect('2fa')->with('error', 'Invalid Verification Code');
  	}
  }
  public function disable2fa(Request $request){

  	$validatedData = $request->validate([
  		'current-password' => 'required',
  	]);
  	$user = Auth::user();
  	 if(!(Hash::check($request->get('current-password'), $user->password))){
  		return redirect()->back()->with('error', ' Password Does not Match ');
  	}
  	$user->passwordSecurity->google2fa_enable = 0;
  	$user->passwordSecurity->save();
  	return redirect('2fa')->with('success', "2FA is now Disabled Successfully");
  }
}
