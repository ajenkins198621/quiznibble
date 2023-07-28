import { useState, useEffect } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { PageProps } from '@/types';
import FlaggedQuestionRow from './FlaggedQuestionRow';

type Question = {
    id: number;
    question: string;
    active: boolean;
    flagged: boolean;
    flagged_reason: string;
}

type Props = PageProps & {
    questions: Question[];
}

export default function FlaggedQuestion({
    auth,
    questions,
}: Props) {

    const [flaggedQuestions, setFlaggedQuestions] = useState<Question[]>([]);

    useEffect(() => {
        setFlaggedQuestions(questions)
    }, [questions]);

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Flagged Questions</h2>}
        >
            <Head title="Flagged Questions" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                    <div className="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                        <div className="overflow-x-auto">
                            <table className="table table-zebra">
                                {/* head */}
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Question</th>
                                        <th>Reason</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {
                                        questions.map((question) => (
                                            <FlaggedQuestionRow
                                                key={question.id}
                                                questionId={question.id}
                                                question={question.question}
                                                reason={question.flagged_reason}
                                                flagged={question.flagged}
                                                active={question.active}
                                                handleUpdatedQuestion={(questionId, flagged, active) => {
                                                    setFlaggedQuestions([...flaggedQuestions.map((question) => {
                                                        if (question.id === questionId) {
                                                            return {
                                                                ...question,
                                                                flagged,
                                                                active
                                                            }
                                                        }
                                                        return question;
                                                    })])
                                                }}
                                            />
                                        ))
                                    }
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </AuthenticatedLayout>
    );
}
