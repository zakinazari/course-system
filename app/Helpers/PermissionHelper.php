<?php
use App\Models\Settings\Menu;
use App\Models\Settings\Permission;

if (!function_exists('read')) {
    function read($role=[], $menu_id=null) {
		if (!is_array($role)) {
            $role = explode(',', $role);
        }
    	if($menu_id != null){
			$user_permission=Permission::whereIn('role_id',$role)
			->where('menu_id',$menu_id)
			->where('action_id',1)
			->first();
			if($user_permission != null){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
    }
}

if (!function_exists('add')) {
    function add($role=[], $menu_id=null) {

		if (!is_array($role)) {
            $role = explode(',', $role);
        }
    	if($menu_id != null){
			$user_permission=Permission::whereIn('role_id',$role)
			->where('menu_id',$menu_id)
			->where('action_id',2)
			->first();
			if($user_permission != null){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
    }
}
if (!function_exists('edit')) {
    function edit($role=[], $menu_id=null) {
		if (!is_array($role)) {
            $role = explode(',', $role);
        }
    	if($menu_id != null){
			$user_permission=Permission::whereIn('role_id',$role)
			->where('menu_id',$menu_id)
			->where('action_id',3)
			->first();
			if($user_permission != null){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
    }
}
if (!function_exists('delete')) {
    function delete($role=[], $menu_id=null) {
		if (!is_array($role)) {
            $role = explode(',', $role);
        }
    	if($menu_id != null){
			$user_permission=Permission::whereIn('role_id',$role)
			->where('menu_id',$menu_id)
			->where('action_id',4)
			->first();
			if($user_permission != null){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
    }
}
if (!function_exists('confirm')) {
    function confirm($role=[], $menu_id=null) {
		if (!is_array($role)) {
            $role = explode(',', $role);
        }
    	if($menu_id != null){
			$user_permission=Permission::whereIn('role_id',$role)
			->where('menu_id',$menu_id)
			->where('action_id',5)
			->first();
			if($user_permission != null){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
    }
}
if (!function_exists('unconfirm')) {
    function unconfirm($role=[], $menu_id=null) {
		if (!is_array($role)) {
            $role = explode(',', $role);
        }
    	if($menu_id != null){
			$user_permission=Permission::whereIn('role_id',$role)
			->where('menu_id',$menu_id)
			->where('action_id',6)
			->first();
			if($user_permission != null){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
    }
}

if (!function_exists('getImage')) {
    function getImage($path) {
    
        $default = 'assets/images/defaultStudent.png';

      
        if (empty($path) || $path == '0') {
            if (Storage::disk('local')->exists($default)) {
                $file = Storage::disk('local')->get($default);
                $mimeType = Storage::disk('local')->mimeType($default);
                $imageData = base64_encode($file);
                return "data:$mimeType;base64,$imageData";
            }
            return null; 
        }

   
        if (Storage::disk('local')->exists($path)) {
            $file = Storage::disk('local')->get($path);
            $mimeType = Storage::disk('local')->mimeType($path);
            $imageData = base64_encode($file);
            return "data:$mimeType;base64,$imageData";
        }


        if (Storage::disk('local')->exists($default)) {
            $file = Storage::disk('local')->get($default);
            $mimeType = Storage::disk('local')->mimeType($default);
            $imageData = base64_encode($file);
            return "data:$mimeType;base64,$imageData";
        }

        return null;
    }
}