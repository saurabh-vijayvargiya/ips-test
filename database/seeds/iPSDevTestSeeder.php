<?php

use App\Course;
use Illuminate\Database\Seeder;
use App\Module;

class iPSDevTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Inserting courses.
        Course::insert([
          [
            'id' => 'ipa',
            'name' => 'iPhone Photo Academy'
          ],

          [
            'id' => 'iea',
            'name' => 'iPhone Editing Academy'
          ],

          [
            'id' => 'iaa',
            'name' => 'iPhone Art Academy'
          ]
        ]);

        // Inserting modules in the modules table.
        for ($i = 1; $i <= 7; $i++) {
            Module::insert([
                [
                    'course_key' => 'ipa',
                    'name' => 'IPA Module ' . $i
                ],

                [
                    'course_key' => 'iea',
                    'name' => 'IEA Module ' . $i
                ],

                [
                    'course_key' => 'iaa',
                    'name' => 'IAA Module ' . $i
                ]
            ]);
        }
    }
}
