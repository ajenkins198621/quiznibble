import { TagsThatNeedReviewType } from "./Container";

type Props = {
    score: number;
    latestPointsEarned: number;
    currentQuestionsLength: number;
    tagsThatNeedReview: TagsThatNeedReviewType;
    onResetQuiz: () => void;

}

export default function QuizResults({
    score,
    latestPointsEarned,
    currentQuestionsLength,
    tagsThatNeedReview,
}: Props) {

    return (
        <div className='flex flex-col mt-4'>
            <div className='text-center text-xl border-b pb-4'>
                Your score is {Math.floor((score / currentQuestionsLength) * 100)}%
            </div>
            {
                latestPointsEarned > 0 && (
                    <div className='text-center text-accent text-2xl font-extrabold border-b py-4'>
                        <span className="animate-pulse">+</span>{latestPointsEarned} points
                    </div>
                )
            }
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

            <a href="/dashboard" className='text-center mt-6 bg-accent px-4 py-2 rounded'>Back</a>
        </div>

    )
}
