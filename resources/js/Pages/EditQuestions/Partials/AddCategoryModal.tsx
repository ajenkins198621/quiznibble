import { FormEventHandler, useState } from 'react';
import Modal from '@/Components/Modal';
import axios from 'axios';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';
import { EditCategoryProp } from '@/types';


interface IProps {
    show: boolean;
    type: 'primaryCategory' | 'subCategory' | 'tag';
    parentId: number;
    onClose: (categoryData?: {
        categories: EditCategoryProp[];
        subCategories: EditCategoryProp[];
    }) => void;
}

export default function AddCategoryModal({
    show = false,
    type,
    parentId = -1,
    onClose,
}: IProps) {

    const [submitting, setSubmitting] = useState<boolean>(false);
    const [name, setName] = useState<string>('');

    let data: {
        title: string;
    } = {
        title: '',
    }

    switch (type) {
        case 'primaryCategory':
            data.title = 'Create a Primary Category';
            break;
        case 'subCategory':
            data.title = 'Create a Sub Category';
            break;
        case 'tag':
            data.title = 'Create a Tag';
            break;
    }

    const handleSubmit: FormEventHandler = (e) => {
        e.preventDefault()
        setSubmitting(true);
        axios.post(route('dashboard.questions.add-category-or-tag'), {
            type,
            parentId,
            name,
        })
            .then((res : {
                data: {
                    message: string;
                    categories: EditCategoryProp[];
                    sub_categories: EditCategoryProp[];
                }
            }) => {
                setName('');
                onClose({
                    categories: res.data.categories,
                    subCategories: res.data.sub_categories,
                });
            })
            .catch(err => {
                alert('Something went wrong. Please try again later.');
            })
            .finally(() => {
                setSubmitting(false);
            });
    }



    return (
        <Modal
            show={show}
            maxWidth={'lg'}
            closeable={true}
            onClose={onClose}
        >
            <div className='p-6'>
                <h2 className="text-lg font-medium text-gray-900 mb-2">{data.title}</h2>
                <form onSubmit={handleSubmit} method='post'>
                    <input
                        type="text"
                        disabled={submitting}
                        value={name}
                        placeholder='Enter a name for the category'
                        onChange={(e) => setName(e.target.value)}
                        className="mt-1 block w-full shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md"
                    />

                    <div className="flex items-center gap-4 mt-3">
                        <PrimaryButton
                            disabled={submitting || !name}
                        >
                            Save
                        </PrimaryButton>
                        <SecondaryButton onClick={() => onClose()}>
                            Cancel
                        </SecondaryButton>
                    </div>
                </form>

            </div>
        </Modal >
    )
}
