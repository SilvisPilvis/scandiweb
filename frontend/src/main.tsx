import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import './main.css'
import { QueryClient, QueryClientProvider } from '@tanstack/react-query'
import { ReactQueryDevtools } from '@tanstack/react-query-devtools'

import { RouterProvider, createRouter } from '@tanstack/react-router'
import { CartProvider } from 'react-use-cart'

// Import the generated route tree
import { routeTree } from './routeTree.gen'
import logger from './components/logger'

// Create a new router instance
const router = createRouter({ routeTree })

// Register the router instance for type safety
declare module '@tanstack/react-router' {
    interface Register {
        router: typeof router
    }
}

const queryClient = new QueryClient()

logger.info('App started')

createRoot(document.getElementById('root')!).render(
    <StrictMode>
        <CartProvider>
            <QueryClientProvider client={queryClient}>
                <RouterProvider router={router} />
                <ReactQueryDevtools />
            </QueryClientProvider>
        </CartProvider>
    </StrictMode>,
)
