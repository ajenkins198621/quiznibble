import { useEffect, useState } from 'react';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import { Link, useForm, usePage } from '@inertiajs/react';
import { Transition } from '@headlessui/react';
import { FormEventHandler } from 'react';
import { EditCategoryProp, EditTagProp } from "@/types";
import { router } from '@inertiajs/react'
import axios from 'axios';
import AddCategoryModal from './AddCategoryModal';

type Props = {
    categories: EditCategoryProp[];
    subCategories: EditCategoryProp[];
}
export default function CreateQuestionsForm({
    categories,
    subCategories,
}: Props) {
    const [availableCategories, setAvailableCategories] = useState<EditCategoryProp[]>(categories);
    const [availableSubCategories, setAvailableSubCategories] = useState<EditCategoryProp[]>([]);
    const [availableTags, setAvailableTags] = useState<EditTagProp[]>([]);

    const [selectedCategory, setSelectedCategory] = useState<string>('');
    const [selectedSubCategory, setSelectedSubCategory] = useState<string>('');
    const [selectedTag, setSelectedTag] = useState<string>('');

    const [questionJson, setQuestionJson] = useState<any>("");
    const [mediaUrl, setMediaUrl] = useState<string>("");
    const [code, setCode] = useState<string>(""); // TODO Add DB field

    const [submitting, setSubmitting] = useState<boolean>(false);
    const [successMessage, setSuccessMessage] = useState<string>('');

    const [modalData, setModalData] = useState<any>({
        show: false,
        type: '',
        partentId: -1,
    });


    const refreshForm = () => {
        setSelectedCategory('');
        setSelectedSubCategory('');
        setSelectedTag('');
        setQuestionJson('');
        setMediaUrl('');
        setCode('');
    }

    const handleCategoriesChange = (categories: EditCategoryProp[] = [], subCategories: EditCategoryProp[]) => {
        if (categories.length > 0) {
            setAvailableCategories(categories);
        }
        if (selectedCategory !== '') {
            setAvailableSubCategories(subCategories.filter(subCategory => subCategory.parent_id == Number(selectedCategory)));

            if (selectedSubCategory !== '') {
                console.log({
                    subCategories
                })
                const subCategory = subCategories.filter(subCategory => subCategory.id == Number(selectedSubCategory))[0];
                if (subCategory && typeof subCategory.tags !== "undefined" && subCategory.tags.length > 0) {
                    setAvailableTags(subCategory.tags);
                }
            } else {
                setSelectedTag('');
                setAvailableTags([]);
            }

        } else {
            setSelectedSubCategory('');
            setSelectedTag('');
            setAvailableSubCategories([]);
            setAvailableTags([]);
        }
    }

    useEffect(() => {
        handleCategoriesChange([], subCategories);
    }, [selectedCategory, selectedSubCategory, selectedTag]);



    const handleSubmit: FormEventHandler = (e) => {
        e.preventDefault()
        setSubmitting(true);
        setSuccessMessage('')
        axios.post(route('dashboard.questions.add-question'), {
            category_id: selectedCategory,
            subcategory_id: selectedSubCategory,
            tag_id: selectedTag,
            json: questionJson,
            mediaUrl: mediaUrl,
            code: code,
        })
            .then(res => {
                setSubmitting(false);
                setSuccessMessage(res.data.message);
                refreshForm();
            })
    }

    const handleModal = (type: 'primaryCategory' | 'subCategory' | 'tag', parentId: number = -1) => {
        setModalData({
            show: true,
            type: type,
            partentId: parentId ? parentId : -1,
        });
    }


    return (
        <section>
            <header>
                <h2 className="text-lg font-medium text-gray-900">Create Questions</h2>

                <p className="mt-1 text-sm text-gray-600">
                    Here you can create questions.
                </p>

                {
                    successMessage !== '' && (
                        <div className="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span className="block sm:inline">{successMessage}</span>
                        </div>
                    )
                }
            </header>

            <form onSubmit={handleSubmit} method='post' className="mt-6 space-y-6">
                <div>
                    <InputLabel htmlFor="primaryCategory" value="">
                        Primary Category <sup className='text-red-500'>*</sup>
                    </InputLabel>

                    <select
                        id="primaryCategory"
                        className="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        value={selectedCategory}
                        onChange={(e) => setSelectedCategory(e.target.value)}
                        required
                    >
                        <option value="">--- Select Primary Category ---</option>
                        {
                            availableCategories.map((category) => (
                                <option
                                    key={category.id}
                                    value={category.id}
                                >
                                    {category.category_name}
                                </option>
                            ))
                        }
                    </select>
                    <br />
                    <a onClick={() => handleModal('primaryCategory')} className="text-blue-500 hover:text-blue-800 text-sm underline  cursor-pointer">Add Primary Category</a>
                </div>
                {
                    !!selectedCategory && (
                        <div>
                            <InputLabel htmlFor="subCategory" value="">
                                Sub Category <sup className='text-red-500'>*</sup>
                            </InputLabel>

                            <select
                                id="subCategory"
                                className="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                value={selectedSubCategory}
                                onChange={(e) => setSelectedSubCategory(e.target.value)}
                                required
                            >
                                <option value="">--- Select Sub Category --- </option>
                                {
                                    availableSubCategories.map((category) => (
                                        <option
                                            key={category.id}
                                            value={category.id}
                                        >
                                            {category.category_name}
                                        </option>
                                    ))
                                }
                            </select>
                            <br />
                            <a onClick={() => handleModal('subCategory', Number(selectedCategory))} className="text-blue-500 hover:text-blue-800 text-sm underline cursor-pointer">Add Sub Category</a>
                        </div>
                    )
                }


                {
                    !!selectedCategory && !!selectedSubCategory && (
                        <div>
                            <InputLabel htmlFor="tag" value="">
                                Tag <sup className='text-red-500'>*</sup>
                            </InputLabel>

                            <select
                                id="tag"
                                className="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                value={selectedTag}
                                onChange={(e) => setSelectedTag(e.target.value)}
                                required
                            >
                                <option value="">--- Select Tag --- </option>
                                {
                                    availableTags.map((tag) => (
                                        <option
                                            key={tag.id}
                                            value={tag.id}
                                        >
                                            {tag.tag_name}
                                        </option>
                                    ))
                                }
                            </select>
                            <br />
                            <a onClick={() => handleModal('tag', Number(selectedSubCategory))} className="text-blue-500 hover:text-blue-800 text-sm underline cursor-pointer">Add Tag</a>

                        </div>
                    )
                }

                {
                    selectedCategory !== '' && selectedSubCategory !== '' && selectedTag !== '' && (
                        <>
                            <div>
                                <InputLabel htmlFor="mediaUrl" value="">
                                    Media URL - Image URL, etc (Optional)
                                </InputLabel>

                                <TextInput
                                    id="mediaUrl"
                                    type="text"
                                    name="mediaUrl"
                                    placeholder="Enter media URL here"
                                    value={mediaUrl}
                                    className="w-1/2 mt-1 block w-full"
                                    onChange={(e) => setMediaUrl(e.target.value.trim())}
                                />
                            </div>

                            <div>
                                <InputLabel htmlFor="code" value="">
                                    Code - will display code as part of question (keep shortish) (Optional)<br />


                                    <textarea
                                        id="code"
                                        name="code"
                                        placeholder='Enter optional code here'
                                        value={code}
                                        onChange={(e) => setCode(e.target.value.trim())}
                                        rows={3}
                                        className="w-1/2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    />
                                </InputLabel>
                            </div>

                            <div>
                                <InputLabel htmlFor="questionJson" value="">
                                    JSON - Question JSON (Required) <sup className='text-red-500'>*</sup><br />
                                    <textarea
                                        id="questionJson"
                                        name="questionJson"
                                        placeholder='Enter question JSON here'
                                        value={questionJson}
                                        onChange={(e) => setQuestionJson(e.target.value.trim())}
                                        rows={4}
                                        className="w-1/2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    />
                                </InputLabel>
                            </div>



                        </>
                    )
                }

                <div className="flex items-center gap-4">
                    <PrimaryButton
                        disabled={!selectedCategory || !selectedSubCategory || !selectedTag || !questionJson || submitting}
                    >
                        Save
                    </PrimaryButton>

                    <Transition
                        show={!!successMessage}
                        enter="transition ease-in-out"
                        enterFrom="opacity-0"
                        leave="transition ease-in-out"
                        leaveTo="opacity-0"
                    >
                        <p className="text-sm text-gray-600">Saved.</p>
                    </Transition>
                </div>


            </form>

            <AddCategoryModal
                show={modalData.show}
                type={modalData.type}
                parentId={modalData.partentId}
                onClose={(categoryData) => {
                    if (categoryData && categoryData.categories && categoryData.subCategories) {
                        handleCategoriesChange(categoryData.categories, categoryData.subCategories);
                    }
                    setModalData({ show: false, type: '', partentId: -1 });
                }}
            />
        </section>
    )
}
