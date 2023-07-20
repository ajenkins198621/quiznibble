<?php
use App\Models\User;
use App\Models\Question;

it('can store user answers', function () {
    // Assuming you have User, Question models and users, questions tables
    // Also assuming you have authentication and can get an authenticated user

    $user = User::factory()->create();
    $questions = Question::factory()->count(3)->create();

    $payload = [
        'answers' => [
            ['question_id' => $questions[0]->id, 'is_correct' => true],
            ['question_id' => $questions[1]->id, 'is_correct' => false],
            ['question_id' => $questions[2]->id, 'is_correct' => true]
        ]
    ];

    $response = $this->actingAs($user)->postJson('/api/answer-quiz', $payload);

    // Assert it was a successful request
    $response->assertStatus(201);

    // Assert the database has the expected data
    $this->assertDatabaseHas('user_question_responses', [
        'user_id' => $user->id,
        'question_id' => $questions[0]->id,
        'correct_count' => 1,
        'incorrect_count' => 0,
        'attempt_count' => 1,
    ]);

    $this->assertDatabaseHas('user_question_responses', [
        'user_id' => $user->id,
        'question_id' => $questions[1]->id,
        'correct_count' => 0,
        'incorrect_count' => 1,
        'attempt_count' => 1,
    ]);

    $this->assertDatabaseHas('user_question_responses', [
        'user_id' => $user->id,
        'question_id' => $questions[2]->id,
        'correct_count' => 1,
        'incorrect_count' => 0,
        'attempt_count' => 1,
    ]);

    // Assert the response has the expected data
    $response->assertJson(['message' => 'Responses saved successfully.']);
});
