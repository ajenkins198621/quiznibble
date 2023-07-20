import { useState, useEffect } from "react";
import { Answer, Question as QuestionType } from "../../types/quiz";

function shuffleArray(array: Answer[]) {
    for (let i = array.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        const temp = array[i];
        array[i] = array[j];
        array[j] = temp;
    }
}

interface IProps {
    question: QuestionType;
    submitted: boolean;
    handleAnswer: (isCorrect: boolean, hint: string) => void;
}

function Question(props: IProps) {
    const { question, submitted, handleAnswer } = props;

    const [selectedAnswer, setSelectedAnswer] = useState<number>(-1); // -1 means no answer selected
    const [answers, setAnswers] = useState<Answer[]>([]);
    useEffect(() => {
        const answers: Answer[] = [...question.answers];
        shuffleArray(answers);
        setAnswers(answers);
    }, [question.answers]);

    useEffect(() => {
        setSelectedAnswer(-1);
    }, [question]);

    const getSelectedClasses = (idx: number, is_correct: boolean): string => {
        if (selectedAnswer !== idx) {
            if(submitted) {
                return "bg-gray-300 hover:bg-gray-300 text-gray-500 hover:text-gray-500 hover:border-gray-100 cursor-not-allowed";
            } else {
                return "";
            }
        }
        // Selected
        if(!submitted) {
            return "bg-gray-700 hover:bg-gray-700 border-gray-800 hover:border-gray-800 text-white animate-pulse";
        }
        if (selectedAnswer === idx && is_correct) {
            return "bg-green-300 text-green-800 border-green-800 hover:bg-green-300 hover:green-800 hover:border-green-800 cursor-not-allowed";
        }
        return "bg-red-300 text-red-800 border-red-800 hover:bg-red-300 hover:red-800 hover:border-red-800 cursor-not-allowed";
    };

    return (
        <div>
            <h2 className="text-xl text-gray-600 font-black leading-tight mt-4">{question.question}</h2>
            {
                answers.map(({answer, is_correct}, i) => (
                    <div
                        key={i}
                        className={`w-full p-3 border my-4 rounded-lg hover:border-gray-800 hover:bg-gray-100 cursor-pointer ${getSelectedClasses(i, is_correct == 1)}`}
                        onClick={() => {
                            if(submitted) {
                                return;
                            }
                            setSelectedAnswer(i);
                            handleAnswer(is_correct == 1, question.hint)
                        }}
                    >
                        <div dangerouslySetInnerHTML={{ __html: answer }} />
                    </div>
                ))
            }
        </div>
    )
}

export default Question;
