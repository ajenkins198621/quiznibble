import { useEffect, useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { PageProps } from '@/types';
import Container from './Quiz/Container';

type Props = PageProps & {
    mainCategoryId: number;
    subCategoryId: number;
    categories: {
        id: number;
        category_name: string;
        sub_categories: {
            id: number;
            category_name: string;
            parent_id: number;
        }[]
        question_count: null | {
            category_id: number;
            question_count: number;
        }
    }[];
}

export default function Dashboard({
    auth,
    mainCategoryId,
    subCategoryId,
    categories
}: Props) {
    const [selectedCategoryId, setSelectedCategoryId] = useState<number>(-1);
    const [selectedSubCategoryId, setSelectedSubCategoryId] = useState<number>(-1);
    const [quizEnabled, setQuizEnabled] = useState<boolean>(false);

    useEffect(() => {
        setSelectedCategoryId(mainCategoryId);
        setSelectedSubCategoryId(subCategoryId);
        if (mainCategoryId !== -1) {
            setQuizEnabled(true);
        }
    }, [mainCategoryId, subCategoryId]);

    console.log({ categories });


    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>}
        >
            <Head title="Dashboard" />

            {/* <div className="pt-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900">Coming Soon: Select your categories (need this ASAP!)</div>
                    </div>
                </div>
            </div> */}
            <div className="pt-12 mb-12">
                <div className="max-w-xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        {
                            !quizEnabled ? (
                                <div className='p-6'>

                                    <h2 className='text-lg font-medium text-gray-900 mb-4 border-b pb-2'>Select a quiz:</h2>
                                    <div className='flex flex-col space-y-6'>
                                    {
                                        categories.filter(({question_count}) => {
                                            if(!question_count || !question_count.question_count) return false;
                                            return true;
                                        }).map((category) => (
                                            <button
                                                key={category.id}
                                                className="btn btn-block btn-accent"
                                                onClick={() => {
                                                    setSelectedCategoryId(category.id);
                                                    setQuizEnabled(true);
                                                }}
                                            >
                                                {category.category_name}
                                            </button>
                                        ))
                                    }
                                    </div>

                                </div>
                            )
                            : (
                                <Container
                                    mainCategoryId={selectedCategoryId}
                                    subCategoryId={selectedSubCategoryId}
                                />
                            )
                        }
                    </div>
                </div>
            </div>

        </AuthenticatedLayout>
    );
}
