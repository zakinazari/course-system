<?php
use App\Models\Settings\Menu;
use App\Models\Settings\Permission;
use Morilog\Jalali\Jalalian;
if (!function_exists('read')) {
    function read($role = [], $menu_id = null)
    {
        $currentUser = Auth::user();
    
        if ($currentUser && $currentUser->isDeveloper()) {
            return true;
        }

        if (!is_array($role)) {
            $role = explode(',', $role);
        }

        if ($menu_id != null) {
            $user_permission = Permission::whereIn('role_id', $role)
                ->where('menu_id', $menu_id)
                ->where('action_id', 1)
                ->first();

            return $user_permission != null;
        }

        return false;
    }
}

if (!function_exists('add')) {
    function add($role=[], $menu_id=null) {

		$currentUser = Auth::user();
    
        if ($currentUser && $currentUser->isDeveloper()) {
            return true;
        }

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
		$currentUser = Auth::user();
    
        if ($currentUser && $currentUser->isDeveloper()) {
            return true;
        }

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
		$currentUser = Auth::user();
    
        if ($currentUser && $currentUser->isDeveloper()) {
            return true;
        }

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
		$currentUser = Auth::user();
    
        if ($currentUser && $currentUser->isDeveloper()) {
            return true;
        }

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
		$currentUser = Auth::user();
    
        if ($currentUser && $currentUser->isDeveloper()) {
            return true;
        }
		
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

    if (!function_exists('getLogo')) {
        function getLogo() {
            $path = public_path('logo.png');

            if(file_exists($path)){
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            } else {
                $base64 = null;
            }
            return $base64;
        }
    }

    // 
    if (!function_exists('jalaliToGregorianMonthRange')) {
        // year = jalai year, month= jalali month
        function jalaliToGregorianMonthRange($year, $month)
        {
            $month = str_pad($month, 2, '0', STR_PAD_LEFT);

            $date = "{$year}-{$month}-01";

            $start = Jalalian::fromFormat('Y-m-d', $date)
                ->toCarbon()
                ->startOfDay();

            $end = Jalalian::fromFormat('Y-m-d', $date)
                ->addMonths(1)
                ->subDay()
                ->toCarbon()
                ->endOfDay();

            return [$start, $end];
        }
    }

    if (! function_exists('tax')) {

        function tax($salary)
        {
            if ($salary <= 5000) {
                return 0;
            }

            if ($salary <= 12500) {
                return ($salary - 5000) * 0.02;
            }

            if ($salary <= 100000) {
                return 150 + (($salary - 12500) * 0.10);
            }

            return 8900 + (($salary - 100000) * 0.20);
        }
    }
}