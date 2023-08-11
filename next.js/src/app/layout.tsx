import Header from "@/components/layout/Header";

import type { Metadata } from 'next'

export const metadata: Metadata = {
  title: 'Drupal Headless',
  description: 'Headless rendering of Drupal contents using Next.js front-end in this experimental repository.'
}

export default function RootLayout({
  children,
}: {
  children: React.ReactNode
}) {
  return (
    <html lang="en">
      <body>
        <Header />

        {children}
      </body>
    </html>
  )
}
