import ArticleTeaser from "@/components/ArticleTeaser";

import type {Article} from "@/types";

async function getArticles() {
  const res = await fetch('http://drupal10.ddev.site/api/v1/articles');

  if (!res.ok) {
    throw new Error('Unable to load articles.');
  }

  return res.json();
}

export default async function ArticlesList() {
  const data = await getArticles();
  const articles = data.articles;

  return (
    <ul>
      {articles.map((article: Article) => {
        return (
          <li key={article.id}>
            <ArticleTeaser article={article}/>
          </li>
        )
      })}
    </ul>
  )
}
