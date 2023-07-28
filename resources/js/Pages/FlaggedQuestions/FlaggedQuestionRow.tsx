import { useState, useEffect } from 'react';
import axios from 'axios';
import { BiHappyAlt, BiHide } from 'react-icons/bi';

type Props = {
    questionId: number;
    question: string;
    reason: string;
    flagged: boolean;
    active: boolean;
    handleUpdatedQuestion: (questionId: number, flagged: boolean, active: boolean) => void;
}
export default function FlaggedQuestionRow({
    questionId,
    question,
    reason,
    flagged,
    active,
    handleUpdatedQuestion,
}: Props) {
    const [submitting, setSubmitting] = useState<boolean>(false);
    const [isFlagged, setIsFlagged] = useState<boolean>(false);
    const [isActive, setIsActive] = useState<boolean>(false);

    useEffect(() => {
        setIsFlagged(flagged);
        setIsActive(active);
    }, []);

    const toggleItem = (type: 'flagged' | 'active', value: 0 | 1) => {
        setSubmitting(true);
        axios.patch(route('dashboard.questions.edit-question.toggle-active'), {
            questionId,
            type,
            value
        }).then((response) => {
            const { data } = response;
            if (data.success) {
                setIsFlagged(!!data.flagged);
                setIsActive(!!data.active);
            } else {
                alert('There was an error marking the question as active/inactive. Please try again.')
            }
        }).finally(() => {
            setSubmitting(false);
        });
    }

    return (
        <tr>
            <th>{questionId}</th>
            <td>{question}</td>
            <td>{reason}</td>
            <td>
                <button
                    className="btn btn-success btn-sm mr-4"
                    onClick={() => toggleItem('flagged', isFlagged ? 0 : 1)}
                    disabled={submitting}
                >
                    <BiHappyAlt />
                    {isFlagged ? 'Unflag' : 'Re-flag'}
                </button>
                <button
                    className="btn btn-error btn-sm"
                    onClick={() => toggleItem('active', isActive ? 0 : 1)}
                    disabled={submitting}
                >
                    <BiHide />
                    Mark{' '}{isActive ? 'Inactive' : 'Active'}
                </button>
            </td>
        </tr>
    )
}
