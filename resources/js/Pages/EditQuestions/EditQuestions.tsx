import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { EditCategoryProp, PageProps } from '@/types';
import CreateQuestionsForm from './Partials/CreateQuestionsForm';

type Props = PageProps & {
    categories: EditCategoryProp[];
    sub_categories: EditCategoryProp[];
}

export default function EditQuestions({
    auth,
    categories,
    sub_categories,

}: Props) {
    console.log(sub_categories);
    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Edit Questions</h2>}
        >
            <Head title="Edit Questions" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                    <div className="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                        <CreateQuestionsForm
                            categories={categories}
                            subCategories={sub_categories}
                        />
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
