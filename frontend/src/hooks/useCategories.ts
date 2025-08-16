// hooks/useCategories.ts
import { useState, useEffect } from 'react'

async function getCategories(){
    const response = await fetch(import.meta.env.VITE_API_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            query: `
        {
            getCategories {
                name
            }
        }
            `,
        })
    })
    return response.json()
}

export function useCategories() {
    const [categories, setCategories] = useState<string[]>([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        async function fetchCategories() {
            try {
                const response = await getCategories();
                if (response && response.data && Array.isArray(response.data.getCategories)) {
                    setCategories(response.data.getCategories.map((cat: { name: string }) => cat.name));
                } else {
                    console.warn("Unexpected API response structure:", response);
                }
            } catch (error) {
                console.error("Error fetching categories:", error);
            } finally {
                setLoading(false); // Ensure loading is set to false
            }
        }
        fetchCategories();
    }, []);
    
    return { categories, loading };
}