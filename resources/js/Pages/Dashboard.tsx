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
        if(mainCategoryId !== -1) {
            setQuizEnabled(true);
        }
    }, [mainCategoryId, subCategoryId]);



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

                    <div className="flex items-center mb-4">
                        <div className='w-1/5'>
                            Select:
                        </div>
                        <div className='w-3/5 flex'>
                            <div className='w-1/2 pr-2'>
                                <select
                                    className='select select-bordered w-full max-w-xs'
                                    value={selectedCategoryId}
                                    onChange={(e) => {
                                        setSelectedCategoryId(parseInt(e.target.value))
                                        setQuizEnabled(false);
                                    }}
                                >
                                    <option value={-1}>Select Category</option>
                                    {
                                        categories.map((category) => (
                                            <option key={category.id} value={category.id}>{category.category_name}</option>
                                        ))
                                    }
                                </select>
                            </div>
                            <div className='w-1/2 pl-2'>
                                {
                                    selectedCategoryId !== -1 && (
                                        <select
                                            className='select select-bordered w-full max-w-xs'
                                            value={selectedSubCategoryId}
                                            onChange={(e) => {
                                                setSelectedSubCategoryId(parseInt(e.target.value))
                                                setQuizEnabled(false);
                                            }}
                                        >
                                            <option value={-1}>All Sub Categories</option>
                                            {
                                                categories.find((category) => category.id === selectedCategoryId)?.sub_categories.map((subCategory) => (
                                                    <option key={subCategory.id} value={subCategory.id}>{subCategory.category_name}</option>
                                                ))
                                            }
                                        </select>
                                    )
                                }
                            </div>

                        </div>
                        <div className='w-1/5 pl-2'>

                            <button
                                className="btn btn-secondary"
                                disabled={selectedCategoryId === -1}
                                onClick={() => setQuizEnabled(true)}
                            >
                                Take Quiz
                            </button>


                        </div>
                    </div>

                    {
                        quizEnabled && (
                            <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <Container
                                    mainCategoryId={selectedCategoryId}
                                    subCategoryId={selectedSubCategoryId}
                                />
                            </div>
                        )
                    }
                </div>
            </div>

        </AuthenticatedLayout>
    );
}
