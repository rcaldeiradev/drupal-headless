async function getArticle(id: string) {
  const res = await fetch(`http://drupal10.ddev.site/api/v1/articles/${id}`);

  if (!res.ok) {
    throw new Error('Unable to load article.');
  }

  return res.json();
}

export default async function ArticlePage({ params }: { params: { id: string }}) {
  const data = await getArticle(params.id);
  const article = data.article;

  const creationDate = new Date(article.created * 1000).toDateString();

  return (
    <article>
      <h1>{article.title}</h1>
      <div>{creationDate}</div>
      <div>{article.body}</div>
    </article>
  )
}
