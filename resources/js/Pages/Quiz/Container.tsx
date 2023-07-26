import { useState, useEffect } from 'react';
import Question from './Question';
import { Questions } from '@/types/quiz';
import Category from './Category';
import axios from 'axios';

type Props = {
    mainCategoryId: number;
    subCategoryId: number;
}

function Container({
    mainCategoryId,
    subCategoryId
}: Props) {

    const baseUrl = location.protocol + '//' + location.host;

    const [loading, setLoading] = useState<boolean>(true);
    const [currentQuestions, setCurrentQuestions] = useState<Questions>([]);
    const [currentQuestionIdx, setCurrentQuestionIdx] = useState<number>(0);
    const [answeredQuestions, setAnsweredQuestions] = useState<{
        [key: number]: number
    }>({});
    const [score, setScore] = useState<number>(0);
    const [selectedAnswerCorrect, setSelectedAnswerCorrect] = useState<boolean>(false);
    const [submitted, setSubmitted] = useState<boolean>(false);
    const [submitBtnEnabled, setSubmitBtnEnabled] = useState<boolean>(false);
    const [nextBtnEnabled, setNextBtnEnabled] = useState<boolean>(false);
    const [currentHint, setCurrentHint] = useState<string>("");
    const [usersStreak, setUsersStreak] = useState<number>(0);
    const [tagsThatNeedReview, setTagsThatNeedReview] = useState<{
        [key: string]: {
            tag_name: string,
            wrongAnswers: number
        }
    }>({});

    useEffect(() => {
        if (currentQuestions.length > 0) {
            return;
        }
        fetch(`${baseUrl}/api/get-quiz/${mainCategoryId}/${subCategoryId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error, status = ${response.status}`);
                }
                return response.json();
            })
            .then((data: {
                questions: Questions,
                userStreak: number
            }) => {
                const { questions, userStreak } = data;
                setCurrentQuestions(questions)
                setUsersStreak(userStreak);
                setLoading(false);
            })
            .catch(error => console.error('Error fetching data', error));

    }, [currentQuestions.length]);

    const submitResults = () => {
        const answers = Object.keys(answeredQuestions).map((key): {
            question_id: number,
            is_correct: number
        } => {
            const question_id = parseInt(key);
            return {
                question_id,
                is_correct: answeredQuestions[question_id] ? 1 : 0
            }
        });

        axios.post(`${baseUrl}/api/answer-quiz`, {
            answers,
        })
            .then((response: {
                data: {
                    message: string,
                    userStreak: number
                }
            }) => {
                setUsersStreak(response.data.userStreak);
            })
            .catch(e => {
                alert('Something went wrong. Please try again later.');
                if (e instanceof Error) {
                    console.error(`There was an error with the fetch request: ${e.message}`);
                }
            });
    }

    const currentProgress = Math.floor(currentQuestionIdx / currentQuestions.length * 100);

    if (loading) return (
        <div className='flex justify-center items-center p-6'>
            <span className="loading loading-ring loading-lg" />
        </div>

    );

    if (currentQuestions.length === 0) return (
        <div className='p-6 text-center'>
            <p className='strong'>NO QUESTIONS FOUND!</p>
            <p>Try another category...</p>
        </div>

    );


    const currentQuestion = currentQuestions[currentQuestionIdx];

    return (
        <>
            <div className={`pl-6 pt-4 pr-4${!currentQuestion ? ' pb-4' : ''} text-gray-900 flex flex-col bg-gray-800 text-gray-200`}>
                <div className='flex justify-between items-center'>
                    <span className='text-gray-200'>⭐️ {usersStreak} day streak</span>
                    <div className="radial-progress bg-gray-300 text-primary-content border-4 border-gray-200" style={{
                        // @ts-ignore
                        "--value": currentProgress,
                        "--size": "3.5rem"
                    }}>
                        {currentQuestionIdx}/{currentQuestions.length}
                    </div>
                </div>
                {
                    !!currentQuestion && (
                        <Category question={currentQuestion} />
                    )
                }
            </div>
            <div className="pl-6 pb-6 pr-6 text-gray-900">

                {
                    currentQuestionIdx <= currentQuestions.length - 1 ?
                        <>

                            {
                                !!currentQuestion && (
                                    <Question
                                        question={currentQuestion}
                                        handleAnswer={(isCorrect: boolean, hint: string) => {
                                            setSubmitBtnEnabled(true);
                                            setSelectedAnswerCorrect(isCorrect);
                                            setCurrentHint(hint)

                                            const handleAnsweredQuestions = () => {
                                                const newAnsweredQuestions = {
                                                    ...answeredQuestions
                                                };
                                                const questionId = currentQuestion.id;
                                                if (typeof newAnsweredQuestions[questionId] === "undefined") {
                                                    newAnsweredQuestions[questionId] = isCorrect ? 1 : 0;
                                                }
                                                setAnsweredQuestions(newAnsweredQuestions);
                                            }
                                            handleAnsweredQuestions();
                                        }}
                                        submitted={submitted}
                                    />
                                )
                            }
                            <div className='flex justify-between'>
                                <div className='w-1/2 mr-2'>
                                    <button
                                        disabled={!submitBtnEnabled}
                                        className={`btn btn-secondary w-full${submitBtnEnabled ? ' animate-pulse' : ''}`}
                                        onClick={() => {
                                            setSubmitBtnEnabled(false);
                                            setSubmitted(true);
                                            setNextBtnEnabled(true);
                                            if (selectedAnswerCorrect) {
                                                setScore(score + 1);
                                            } else {
                                                // Add mistake to end of array
                                                setCurrentQuestions([
                                                    ...currentQuestions,
                                                    currentQuestions[currentQuestionIdx]
                                                ]);

                                                // Update tags that need more practice
                                                const questionTags = currentQuestion.tags;
                                                const newTagsThatNeedReview = {...tagsThatNeedReview};
                                                questionTags.forEach(tag => {
                                                    if (typeof newTagsThatNeedReview[tag.id] === "undefined") {
                                                        newTagsThatNeedReview[tag.id] = {
                                                            tag_name: tag.tag_name,
                                                            wrongAnswers: 1
                                                        }
                                                    } else {
                                                        newTagsThatNeedReview[tag.id].wrongAnswers = Number(newTagsThatNeedReview[tag.id].wrongAnswers) + 1;
                                                    }
                                                });
                                                setTagsThatNeedReview(newTagsThatNeedReview);

                                            }
                                        }}
                                    >
                                        Submit
                                    </button>
                                </div>

                                <div className='w-1/2 ml-2'>

                                    <button
                                        disabled={!nextBtnEnabled}
                                        className={`btn btn-secondary w-full${nextBtnEnabled ? ' animate-pulse' : ''}`}
                                        onClick={() => {
                                            setCurrentQuestionIdx(currentQuestionIdx + 1);
                                            setNextBtnEnabled(false);
                                            setSubmitted(false);
                                            if (currentQuestionIdx === currentQuestions.length - 1) {
                                                submitResults();
                                            }
                                        }}
                                    >
                                        {
                                            currentQuestionIdx === currentQuestions.length - 1 ?
                                                "Finish"
                                                :
                                                "Next Question"
                                        }
                                    </button>
                                </div>
                            </div>

                            {
                                currentHint !== "" && submitted ?
                                    <div className="bg-blue-300 p-2 mt-4 border border-info-content">
                                        <p className='text-info-content leading-tight'>{currentHint}</p>
                                    </div>
                                    :
                                    null
                            }


                        </>
                        :
                        <div className='flex flex-col mt-4'>
                            <div className='text-center text-xl border-b pb-4'>
                                Your score is {Math.floor((score / currentQuestions.length) * 100)}%
                            </div>
                            {
                                Object.keys(tagsThatNeedReview).length > 0 ?
                                    <div className='mt-6'>
                                        <div>The following tags could use some attention:</div>
                                        <div className='flex flex-row flex-wrap'>
                                            {
                                                Object.keys(tagsThatNeedReview).map((key, idx) => {
                                                    const tag = tagsThatNeedReview[key];
                                                    return (
                                                        <div key={idx} className='bg-blue-300 text-blue-900 px-4 py-1 m-1 rounded'>
                                                            {tag.tag_name} ({tag.wrongAnswers} wrong answers)
                                                        </div>
                                                    )
                                                })
                                            }
                                        </div>
                                    </div>
                                    :
                                    null
                            }
                        </div>
                }
            </div>

        </>
    )
}

export default Container;
