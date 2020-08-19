<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class FollowersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $users = User::all();
        $user = $users->first();
        $user_id = $user->id;

        $followers = $users->slice(1);
        $follower_ids = $followers->pluck('id')->toArray();

        // 1号关注除1号为的所有用户
        $user->follow($follower_ids);

        // 其他人都关注1号
        foreach ($followers as $follower) {
          $follower->follow($user_id);
        }
    }
}
