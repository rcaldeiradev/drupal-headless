
import Link from "next/link";

import type {Article} from "@/types";

export default function ArticleTeaser({ article }: { article: Article }) {

  function buildSummary(summary: string) {
    const summaryCharLimit = 200;

    return summary.length > summaryCharLimit ?
      summary.substring(0, summaryCharLimit) + '...' :
      summary;
  }

  return (
    <article>
      <h2 className="">
        <Link href={`/articles/${article.id}`}>{
          article.title}
        </Link>
      </h2>
      <div className="">{buildSummary(article.summary)}</div>
    </article>
  )
}
