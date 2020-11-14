<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class UsersSeeder extends Seeder
{
    private $photoPath = 'public/fotos';
    private $typesOfUsersDesc =  ['Manager', 'Cook', 'Deliveryman', 'Customer'];
    private $typesOfUsers =  ['EM', 'EC', 'ED', 'C'];
    private $numberOfUsers = [3,    6,    12,   100];
    private $numberOfSoftDeletedUsers = [1, 1, 2, 45];
    private $files_M = [];
    private $files_F = [];
    static public $allUsers = [];

    public function run()
    {
        if (DatabaseSeeder::$seedType == "full") {
            $this->numberOfUsers[3] = 3000;
        } else {
            $this->numberOfUsers[3] = 300;
        }
        $this->command->table(['Users table seeder notice'], [
            ['Photos will be stored on path ' . storage_path('app/' . $this->photoPath)]
        ]);

        UsersSeeder::$allUsers['EM'] = [];
        UsersSeeder::$allUsers['EC'] = [];
        UsersSeeder::$allUsers['ED'] = [];
        UsersSeeder::$allUsers['C'] = [];

        $this->limparFicheirosFotos();
        $this->preencherNomesFicheirosFotos();

        $faker = \Faker\Factory::create('pt_PT');

        for ($typeIdx = 0; $typeIdx < count($this->typesOfUsers); $typeIdx++) {
            $userType = $this->typesOfUsers[$typeIdx];
            $totalUsersOfType = $this->numberOfUsers[$typeIdx];
            $totalSoftDeletes = $this->numberOfSoftDeletedUsers[$typeIdx];
            for ($i = 1; $i <= $totalUsersOfType; $i++) {
                $userNumber = ($userType == 'C') && ($i > 10) ? 0 : $i;
                $userRow = $this->newFakerUser($faker, $userType, $userNumber);
                $userInfo = $this->insertUser($faker, $userRow);
                $this->command->info("Created User '{$this->typesOfUsersDesc[$typeIdx]}' - $i / $totalUsersOfType");
                if ($userNumber > 0) {
                    $this->updateFoto($userInfo);
                }
            }
            $this->softdeletes($userType, $totalSoftDeletes);
            $this->command->info("Soft deleted $totalSoftDeletes users of type '$userType'");
        }
        $this->updateRandomFotos();
    }

    private function limparFicheirosFotos()
    {
        Storage::deleteDirectory($this->photoPath);
        Storage::makeDirectory($this->photoPath);
    }

    private function preencherNomesFicheirosFotos()
    {
        $allFiles = collect(File::files(database_path('seeders/profile_photos')));
        foreach ($allFiles as $f) {
            if (strpos($f->getPathname(), 'M_')) {
                $this->files_M[] = $f->getPathname();
            } else {
                $this->files_F[] = $f->getPathname();
            }
        }
    }

    private function newFakerUser($faker, $tipo = 'C', $userByNumber = 0)
    {
        $gender = $faker->randomElement(['male', 'female']);
        $email = '';
        if ($userByNumber) {
            switch ($tipo) {
                case 'EM':
                    $email = 'manager_' . $userByNumber . '@mail.pt';
                    break;
                case 'EC':
                    $email = 'cook_' . $userByNumber . '@mail.pt';
                    break;
                case 'ED':
                    $email = 'deliveryman_' . $userByNumber . '@mail.pt';
                    break;
                case 'C':
                    $email = 'customer_' . $userByNumber . '@mail.pt';
                    break;
            }
        } else {
            $email = $faker->unique()->safeEmail;
        }

        // Gerar Nome
        $firstname = $faker->firstName($gender);
        $lastname = $faker->lastName();

        $secondname = $faker->numberBetween(1, 3) == 2 ? "" : " " . $faker->firstName($gender);
        $number_middlenames = $faker->numberBetween(1, 6);
        $number_middlenames = $number_middlenames == 1 ? 0 : ($number_middlenames >= 5 ? $number_middlenames - 3 : 1);
        $middlenames = "";
        for ($i = 0; $i < $number_middlenames; $i++) {
            $middlenames .= " " . $faker->lastName();
        }
        $fullname = $firstname . $secondname . $middlenames . " " . $lastname;

        $createdAt = $faker->dateTimeBetween('-10 years', '-3 months');
        $email_verified_at = $faker->dateTimeBetween($createdAt, '-2 months');
        $updatedAt = $faker->dateTimeBetween($email_verified_at, '-1 months');

        return [
            'name' => $fullname,
            'email' =>  $email,
            'email_verified_at' => $email_verified_at,
            'password' => bcrypt('123'),
            'remember_token' => $faker->asciify('**********'), //str_random(10),
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
            'deleted_at' => null,
            'type' => $tipo,
            'blocked' => false,
            'photo_url' => null,
            'logged_at' => null,
            'available_at' => null,
            'gender' => $gender,
        ];
    }

    private function insertUser($faker, $user)
    {
        $userInfo = new \ArrayObject($user);
        $gender = $user['gender'];
        unset($user['gender']);
        $newId = DB::table('users')->insertGetId($user);
        $userInfo['id'] = $newId;
        $userInfo['gender'] = $gender;

        UsersSeeder::$allUsers[$userInfo['type']][$newId] = $userInfo;

        if ($user['type'] == 'C') {
            DB::table('customers')->insert([
                'id' => $newId,
                'address' => $faker->address,
                'phone' =>  $faker->phoneNumber,
                'nif' => $faker->randomNumber($nbDigits = 9, $strict = true),
                'created_at' => $user['created_at'],
                'updated_at' => $user['updated_at'],
                'deleted_at' => $user['deleted_at'],
            ]);
        }
        return $userInfo;
    }

    private static function deleteArrayElement($element, &$array)
    {
        $index = array_search($element, $array);
        if ($index !== false) {
            unset($array[$index]);
        }
    }

    private function gravarFoto($id, $file)
    {
        $targetDir = storage_path('app/' . $this->photoPath);
        //$sourceDir = database_path('seeds/fotos');
        $newfilename = $id . "_" . uniqid() . '.jpg';
        File::copy($file, $targetDir . '/' . $newfilename);
        DB::table('users')->where('id', $id)->update(['photo_url' => $newfilename]);
        $this->command->info("Updated Photo of User $id. File $file copied as $newfilename");
    }

    private function updateFoto($userInfo)
    {
        $fileName = null;
        if ($userInfo['gender'] == 'male') {
            if (count($this->files_M)) {
                $fileName = array_shift($this->files_M);
            }
        } else {
            if (count($this->files_F)) {
                $fileName = array_shift($this->files_F);
            }
        }
        if ($fileName) {
            $this->gravarFoto($userInfo['id'], $fileName);
        }
        return $fileName;
    }

    private function updateRandomFotos()
    {
        $ids = DB::table('users')->whereNull('photo_url')->pluck('id')->toArray();
        while (count($ids) && (count($this->files_F) || count($this->files_M))) {
            shuffle($ids);
            $this->updateFoto(UsersSeeder::$allUsers['C'][array_shift($ids)]);
        }
    }

    private function softdeletes($userType, $totalSoftDeletes)
    {
        $ids = DB::table('users')->where('type', $userType)->pluck('id')->toArray();
        while ($totalSoftDeletes) {
            shuffle($ids);
            $userInfo = UsersSeeder::$allUsers[$userType][array_shift($ids)];
            DB::update('update users set deleted_at = updated_at, blocked=1 where id = ?', [$userInfo['id']]);
            $totalSoftDeletes--;
        }
    }
}
