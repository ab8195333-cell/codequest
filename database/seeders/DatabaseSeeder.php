<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. إنشاء تصنيف افتراضي (Categories)
        $categoryId = DB::table('categories')->insertGetId([
            'name' => 'البرمجة والتطوير',
            'description' => 'تصنيف عام لأسئلة البرمجة',
        ]);

        // 2. إنشاء 10 مستخدمين (Users)
        $userIds = [];
        for ($i = 1; $i <= 10; $i++) {
            $userIds[] = DB::table('users')->insertGetId([
                'name' => "مستخدم تجريبي $i",
                'email' => "user$i@example.com",
                'password' => Hash::make('password'),
                'role' => 'user',
                'is_active' => true,
                'reputation_points' => rand(10, 100),
                'created_at' => now(),
            ]);
        }

        // 3. إنشاء 10 وسوم (Tags)
        $tagIds = [];
        $tagsList = ['php', 'laravel', 'javascript', 'vuejs', 'mysql', 'css', 'html', 'python', 'api', 'bootstrap'];
        foreach ($tagsList as $index => $tagName) {
            $tagIds[] = DB::table('tags')->insertGetId([
                'name' => $tagName,
                'description' => "وسم خاص بـ $tagName",
            ]);
        }

        // 4. إنشاء 10 أسئلة (Questions)
        $questionIds = [];
        for ($i = 1; $i <= 10; $i++) {
            $questionIds[] = DB::table('questions')->insertGetId([
                'title' => "كيف يمكن حل المشكلة البرمجية رقم $i ؟",
                'description' => "هذا نص تجريبي للسؤال رقم $i يشرح المشكلة بالتفصيل وكيفية التعامل معها.",
                'user_id' => $userIds[array_rand($userIds)],
                'category_id' => $categoryId,
                'views_count' => rand(5, 100),
                'created_at' => now(),
            ]);
        }

        // 5. ربط الأسئلة بالوسوم (Question_Tag Table)
        foreach ($questionIds as $qId) {
            // ربط كل سؤال بـ 1 إلى 3 وسوم عشوائية
            $randomTags = (array) array_rand(array_flip($tagIds), rand(1, 3));
            foreach ($randomTags as $tId) {
                DB::table('question_tag')->insert([
                    'question_id' => $qId,
                    'tag_id' => $tId,
                ]);
            }
        }

        // 6. إنشاء 10 إجابات (Answers)
        $answerIds = [];
        for ($i = 1; $i <= 10; $i++) {
            $answerIds[] = DB::table('answers')->insertGetId([
                'answer_text' => "هذه إجابة مقترحة حل للمشكلة رقم $i ويمكنك استخدام الكود التالي لحلها.",
                'question_id' => $questionIds[$i - 1], // ربط كل سؤال بإجابة
                'user_id' => $userIds[array_rand($userIds)],
                'is_accepted' => ($i === 1),
                'created_at' => now(),
            ]);
        }

        // 7. إنشاء 10 تعليقات (Comments)
        for ($i = 1; $i <= 10; $i++) {
            $isQuestion = ($i % 2 === 0);
            DB::table('comments')->insert([
                'content' => "تعليق توضيحي تجريبي رقم $i",
                'user_id' => $userIds[array_rand($userIds)],
                'commentable_id' => $isQuestion ? $questionIds[array_rand($questionIds)] : $answerIds[array_rand($answerIds)],
                'commentable_type' => $isQuestion ? 'App\Models\Question' : 'App\Models\Answer',
                'created_at' => now(),
            ]);
        }

        // 8. إنشاء 10 تصويتات (Votes)
        for ($i = 1; $i <= 10; $i++) {
            $isQuestion = ($i % 2 === 0);
            DB::table('votes')->insert([
                'user_id' => $userIds[array_rand($userIds)],
                'votable_id' => $isQuestion ? $questionIds[array_rand($questionIds)] : $answerIds[array_rand($answerIds)],
                'votable_type' => $isQuestion ? 'App\Models\Question' : 'App\Models\Answer',
                'vote_type' => $isQuestion ? 'up' : 'down',
            ]);
        }
    }
}