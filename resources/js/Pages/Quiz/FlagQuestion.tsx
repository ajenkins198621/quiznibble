import { useState } from 'react';
import axios from 'axios';
import { BsFlagFill, BsFlag } from 'react-icons/bs';

type Props = {
    questionId: number;
}

export default function FlagQuestion({
    questionId,
} : Props) {

    const [viewMessage, setViewMessage] = useState<boolean>(false);
    const [reason, setReason] = useState<string>('');
    const [submitting, setSubmitting] = useState<boolean>(false);

    function submit() {
        setSubmitting(true);
        axios.post(`/api/questions/flag-question`, {
            questionId,
            reason,
        })
            .then(response => {
                setViewMessage(false);
                setSubmitting(false);
            })
            .catch(error => {
                console.log(error);
                alert('There was an error submitting the flag')
            });
    }

    return (
        <div className={`flex flex-col justify-center pt-4`}>
            <button
                className='btn btn-link text-accent font-black'
                onClick={() => {
                    setViewMessage(!viewMessage);
                }}
            >
                <BsFlag className={`mr-2`} />
                Flag Question
            </button>
            {
                viewMessage && (
                    <div className={`flex items-center`}>
                        <textarea
                            className={`textarea textarea-bordered w-full mr-6`}
                            placeholder={`Please provide a reason for flagging this question`}
                            onChange={(e) => {
                                setReason(e.target.value);
                            }}
                            value={reason}
                            disabled={submitting}
                        />
                        <button
                            onClick={submit}
                            className='btn btn-neutral mt-4'
                            disabled={submitting || reason.length < 3}
                        >
                            Submit{submitting ? 'ting' : ''}
                        </button>
                    </div>

                )
            }

        </div>


    )
}
