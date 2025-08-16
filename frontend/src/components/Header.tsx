// components/Header.tsx
import { Link } from '@tanstack/react-router'
import BagIcon from '../icons/Bag'
import { startCase, camelCase } from 'lodash'
import Cart from './Cart';
import { useCategories } from '../hooks/useCategories'

export default function Header() {
    const { categories, loading } = useCategories();

    return (
        <div className="p-2 flex justify-between w-full bg-white px-16 absolute top-0 z-10">
            <div className="flex gap-2 flex-1">
                {!loading && categories.map((name) => (
                    <Link
                        to="/$category"
                        params={{ category: name }}
                        key={name}
                        activeProps={{ 'data-testid': 'active-category-link', 'className': 'font-bold border-b-2 border-lime-500 text-black' }}
                        inactiveProps={{ 'data-testid': 'category-link' }}
                    >
                        {startCase(camelCase(name))}
                    </Link>
                ))}
            </div>
            
            <div className="flex-1 flex justify-center">
                <BagIcon />
            </div>
            
            <div className="flex-1 flex justify-end">
                <Cart />
            </div>
        </div>
    );
}